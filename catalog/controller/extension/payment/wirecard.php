<?php
/**
 * Shop System Plugins - Terms of Use
 *
 * The plugins offered are provided free of charge by Wirecard Central Eastern
 * Europe GmbH
 * (abbreviated to Wirecard CEE) and are explicitly not part of the Wirecard
 * CEE range of products and services.
 *
 * They have been tested and approved for full functionality in the standard
 * configuration
 * (status on delivery) of the corresponding shop system. They are under
 * General Public License Version 2 (GPLv2) and can be used, developed and
 * passed on to third parties under the same terms.
 *
 * However, Wirecard CEE does not provide any guarantee or accept any liability
 * for any errors occurring when used in an enhanced, customized shop system
 * configuration.
 *
 * Operation in an enhanced, customized configuration is at your own risk and
 * requires a comprehensive test phase by the user of the plugin.
 *
 * Customers use the plugins at their own risk. Wirecard CEE does not guarantee
 * their full functionality neither does Wirecard CEE assume liability for any
 * disadvantages related to the use of the plugins. Additionally, Wirecard CEE
 * does not guarantee the full functionality for customized shop systems or
 * installed plugins of other vendors of plugins within the same shop system.
 *
 * Customers are responsible for testing the plugin's functionality before
 * starting productive operation.
 *
 * By installing the plugin into the shop system the customer agrees to these
 * terms of use. Please do not use the plugin if you do not agree to these
 * terms of use!
 */

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . DIR_SYSTEM . '/library/wirecard');

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace("WirecardCEE");

class ControllerExtensionPaymentWirecard extends Controller
{
    protected $data = array();

    private $pluginVersion = '1.5.1';

    private $prefix = 'wirecard';

    const INVOICE_INSTALLMENT_MIN_AGE = 18;

    /**
     * @return mixed
     *
     * provided opencart function
     */
    public function index()
    {
        // set Prefix
        $prefix = $this->prefix . $this->payment_type_prefix;

        // Load required files
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/wirecard');

        if ($prefix != 'wirecard') {
            $this->load->language('extension/payment/wirecard');
        }

        $this->load->language('extension/payment/' . $prefix);

        // additional Data
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['window_name'] = $this->model_extension_payment_wirecard->get_window_name();

        // Set Template
	    $template = 'wirecard_init';

        $data['send_order'] = $this->language->get('send_order');
        $data['error_init'] = $this->language->get('error_init');

	    $data['wcp_ratepay'] = $this->loadRatePay();

        // Set Action URI
        $data['action'] = $this->url->link('extension/payment/' . $prefix . '/init', '', 'SSL');

        // Template Output
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/' . $template . '.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/extension/payment/' . $template . '.tpl';
        } else {
            $this->template = 'extension/payment/' . $template . '.tpl';
        }

        return $this->load->view($this->template, $data);
    }

	/**
	 * create consumerDeviceId script for ratepay
	 *
	 * @return string
	 */
    public function loadRatePay()
    {
	    $customerId = $this->config->get('customerId');

	    if(isset($_SESSION['wcpConsumerDeviceId'])) {
		    $consumerDeviceId = $_SESSION['wcpConsumerDeviceId'];
	    } else {
		    $timestamp = microtime();
		    $consumerDeviceId = md5($customerId . "_" . $timestamp);
		    $_SESSION['wcpConsumerDeviceId'] = $consumerDeviceId;
	    }
	    $ratepay = '<script language="JavaScript">var di = {t:"'.$consumerDeviceId.'",v:"WDWL",l:"Checkout"};</script>';
	    $ratepay .= '<script type="text/javascript" src="//d.ratepay.com/'.$consumerDeviceId.'/di.js"></script>';
	    $ratepay .= '<noscript><link rel="stylesheet" type="text/css" href="//d.ratepay.com/di.css?t='.$consumerDeviceId.'&v=WDWL&l=Checkout"></noscript>';
	    $ratepay .= '<object type="application/x-shockwave-flash" data="//d.ratepay.com/WDWL/c.swf" width="0" height="0"><param name="movie" value="//d.ratepay.com/WDWL/c.swf" /><param name="flashvars" value="t='.$consumerDeviceId.'&v=WDWL"/><param name="AllowScriptAccess" value="always"/></object>';

	    return $ratepay;
    }

    /**
     * payment initialization
     */
    public function init()
    {
        if (isset($_POST['wirecard_checkout_page_window_name'])) {
            $data['window_name'] = $_POST['wirecard_checkout_page_window_name'];
        }

        if (($this->payment_type == WirecardCEE_QPay_PaymentType::INSTALLMENT || $this->payment_type == WirecardCEE_QPay_PaymentType::INVOICE) && !isset($_POST['birthday'])) {
            $this->checkout();
        }

        $birthday = null;
        if ($this->payment_type == WirecardCEE_QPay_PaymentType::INSTALLMENT || $this->payment_type == WirecardCEE_QPay_PaymentType::INVOICE) {
            $birthday = date_create($_POST['birthday']);
            if (!$birthday) {
                $this->checkout();
            }

            $diff = $birthday->diff(new DateTime());
            $customerAge = $diff->format('%y');
            if ($customerAge < self::INVOICE_INSTALLMENT_MIN_AGE) {
                $this->checkout();
            }
        }

	    $financial_institution = NULL;
	    if ($this->payment_type == WirecardCEE_QPay_PaymentType::IDL || $this->payment_type == WirecardCEE_QPay_PaymentType::EPS) {
		    if (isset($_POST['wcp_financialinstitution'])) {
			    $financial_institution = $_POST['wcp_financialinstitution'];
		    }
	    }

        // set Prefix
        $prefix = $this->prefix . $this->payment_type_prefix;

        // Load required files
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/wirecard');

        if ($prefix != 'wirecard') {
            $this->language->load('extension/payment/wirecard');
        }

        $this->language->load('extension/payment/' . $prefix);

        // check payment type
        if (isset($this->payment_type)) {
            $paymentType = $this->payment_type;
        } else {
            $paymentType = 'SELECT';
        }

        // additional Data
        $data['button_confirm'] = $this->language->get('button_confirm');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $pluginVersion = WirecardCEE_QPay_FrontendClient::generatePluginVersion($order_info['store_name'], VERSION,
            'Opencart Wirecard Checkout Page', $this->pluginVersion);

        // set fields, optional, comsumer, generate fingerprint, send request, redirect
        // user
        $result = $this->model_extension_payment_wirecard->sendRequest($prefix, $paymentType, $order_info, $birthday,
            $pluginVersion, $financial_institution);

	    $template = 'wirecard';
        // If connection to wirecard success set template
	    if($result instanceof WirecardCEE_QPay_Error) {
		    $this->session->data['error'] = $result->getMessage();
		    $this->checkout();
	    }
        if ($result) {
            $data['action'] = $result;

            // Check if iframe is active
            if ($this->config->get($prefix . '_iframe') == '1') {
                $template = 'wirecard_iframe';
            } else {
                $this->response->redirect($data['action']);
            }
        } else {
            $this->session->data['error'] = $this->language->get('error_init');
            $this->checkout();
        }

        // Template Output
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/' . $template . '.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/extension/payment/' . $template . '.tpl';
        } else {
            $this->template = 'extension/payment/' . $template . '.tpl';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view($this->template, $data, $this->children));
    }

    /**
     * @return string
     *
     * handles confirmURL
     */
    public function callback()
    {
        // Prefix
        $prefix = $this->prefix . $this->payment_type_prefix;

        // Load required files
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/wirecard');

        $this->model_extension_payment_wirecard->writeLog('confirm_request:' . print_r($_REQUEST, true));

        $message = null;
        if (!isset($_REQUEST['opencartOrderId']) || !strlen($_REQUEST['opencartOrderId'])) {
            $message = 'order-id missing';
            $this->model_extension_payment_wirecard->writeLog($message);
            return WirecardCEE_QPay_ReturnFactory::generateConfirmResponseString($message);
        }
        $order_id = $_REQUEST['opencartOrderId'];

        $order = $this->model_checkout_order->getOrder($order_id);

        $exclude = array(
            'responseFingerprintOrder',
            'responseFingerprint'
        );
        $comment = '';
        foreach ($_POST as $k => $v) {
            if (in_array($k, $exclude)) {
                continue;
            }

            if (is_array($v)) {
                $comment .= "$k:" . print_r($v) . "\n";
            } else {
                $comment .= "$k:$v\n";
            }
        }
        $comment = trim($comment);

        $successStatus = $this->config->get($prefix . '_success_status');
        $pendingStatus = $this->config->get($prefix . '_pending_status');
        $cancelStatus = $this->config->get($prefix . '_cancel_status');
        $failureStatus = $this->config->get($prefix . '_failure_status');

        $message = null;
        try {
            $return = WirecardCEE_QPay_ReturnFactory::getInstance($_POST, $this->config->get($prefix . '_secret'));
            if (!$return->validate()) {
                $message = 'Validation error: invalid response';
                $this->model_checkout_order->addOrderHistory($order_id, $failureStatus);
                $this->model_checkout_order->writeLog($message);
                return WirecardCEE_QPay_ReturnFactory::generateConfirmResponseString($message);
            }

            /**
             * @var $return WirecardCEE_Stdlib_Return_ReturnAbstract
             */
            switch ($return->getPaymentState()) {
                case WirecardCEE_QPay_ReturnFactory::STATE_SUCCESS:
                    // for pending requests, confirm is already done, the model does only one
                    // confirm,
                    // so do an update in this case
                    if ($order['order_status_id']) {
                    	$this->editOrder($order_id, array('order_status_id' => $successStatus));
	                    $this->model_checkout_order->addOrderHistory($order_id, $successStatus, $comment, false);
                    } else {
                        $this->model_checkout_order->addOrderHistory($order_id, $successStatus, $comment, true);
                    }
                    break;
                case WirecardCEE_QPay_ReturnFactory::STATE_PENDING:
                    /**
                     * @var $return WirecardCEE_QPay_Return_Pending
                     */
                    $this->model_checkout_order->addOrderHistory($order_id, $pendingStatus, $comment, true);
                    break;
                // we can do nothing here, confirm() always sends the notification email
                // the update() method doese nothing if the order has not been confirmed yet
                // see catalog/model/checkout/order.php
                case WirecardCEE_QPay_ReturnFactory::STATE_CANCEL:
                    /**
                     * @var $return WirecardCEE_QPay_Return_Cancel
                     */
                    break;

                case WirecardCEE_QPay_ReturnFactory::STATE_FAILURE:
                    /**
                     * @var $return WirecardCEE_QPay_Return_Failure
                     */
                    break;

                default:
                    break;
            }
        } catch (Exception $e) {
            $this->model_checkout_order->addOrderHistory($order_id, $failureStatus);
            $this->model_extension_payment_wirecard->writeLog($e->getMessage());
        }
        echo WirecardCEE_QPay_ReturnFactory::generateConfirmResponseString($message);
    }

	private function editOrder( $order_id, $data ) {
		$query = "UPDATE `" . DB_PREFIX . "order` SET ";
		foreach ( $data as $key => $value ) {
			$query .= $key . " = " . $this->db->escape($value);
			$query .= ", ";
		}
		$query = rtrim( $query, ", ");
		$query .= " WHERE order_id = " .$this->db->escape( $order_id ) . ";";
		$this->db->query( $query );
	}

    /**
     * redirection of successfull payment
     */
    public function success()
    {
        echo '<script type="text/javascript"> parent.location ="' . $this->url->link('checkout/success') . '"</script>';
        echo '<noscript>Javascript muss aktiviert sein</noscript>';
    }

    /**
     * redirection of canceled or not finished payment
     */
    public function checkout()
    {
        echo '<script type="text/javascript"> parent.location ="' . $this->url->link('checkout/checkout') . '"</script>';
        echo '<noscript>Javascript muss aktiviert sein</noscript>';
    }

    /**
     * redirection of failed payment
     */
    public function failure()
    {
        echo '<script type="text/javascript"> parent.location ="' . $this->url->link('checkout/failure') . '"</script>';
        echo '<noscript>Javascript muss aktiviert sein</noscript>';
    }
}
