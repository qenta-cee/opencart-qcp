<?php

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . DIR_SYSTEM . '/library/wirecard');

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace("WirecardCEE");

class ControllerExtensionPaymentWirecard extends Controller {
    protected $data = array();

    private $pluginVersion = '1.3.0';

    private $prefix = 'wirecard';

    const INVOICE_INSTALLMENT_MIN_AGE = 18;

	public function index() {

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
        if ($this->payment_type == WirecardCEE_QPay_PaymentType::INSTALLMENT) {
            $template = 'wirecard_installment';
            $data['txt_info'] = $this->language->get('text_installment_info');
            $data['txt_birthday'] = $this->language->get('text_birthday');
        } elseif ($this->payment_type == WirecardCEE_QPay_PaymentType::INVOICE) {
            $template = 'wirecard_invoice';
            $data['txt_info'] = $this->language->get('text_invoice_info');
            $data['txt_birthday'] = $this->language->get('text_birthday');
        } else {
            $template = 'wirecard_init';
        }

        $data['send_order'] = $this->language->get('send_order');
        $data['error_init'] = $this->language->get('error_init');

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

    public function init()
    {
        /*if (!isset($_POST['wirecard_checkout_page_window_name']) || !isset($_SESSION['order_id'])) {
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }*/
        if (isset($_POST['wirecard_checkout_page_window_name'])) {
            $data['window_name'] = $_POST['wirecard_checkout_page_window_name'];
        }

        if (($this->payment_type == WirecardCEE_QPay_PaymentType::INSTALLMENT || $this->payment_type == WirecardCEE_QPay_PaymentType::INVOICE) && !isset($_POST['birthday'])) {
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
        }

        $birthday = null;
        if ($this->payment_type == WirecardCEE_QPay_PaymentType::INSTALLMENT || $this->payment_type == WirecardCEE_QPay_PaymentType::INVOICE) {
            $birthday = date_create($_POST['birthday']);
            if (!$birthday) {
                $this->response->redirect($this->url->link('checkout/checkout', '', true));
            }

            $diff = $birthday->diff(new DateTime());
            $customerAge = $diff->format('%y');
            if ($customerAge < self::INVOICE_INSTALLMENT_MIN_AGE) {
                $this->response->redirect($this->url->link('checkout/checkout', '', true));
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

        $pluginVersion = WirecardCEE_QPay_FrontendClient::generatePluginVersion($order_info['store_name'], VERSION, 'Opencart Wirecard Checkout Page', $this->pluginVersion);

        //set error_init here
        //$data['error_init'] = $this->language->get('error_init');

        // set fields, optional, comsumer, generate fingerprint, send request, redirect
        // user
        $result = $this->model_extension_payment_wirecard->send_request($prefix, $paymentType, $order_info, $birthday, $pluginVersion);

        // If connection to wirecard success set template
        if ($result) {
            $data['action'] = $result;

            // Check if iframe is active
            if ($this->config->get($prefix . '_iframe') == '1') {
                $template = 'wirecard_iframe';
            } else {
                //// $template = 'wirecard';
                $this->response->redirect($data['action']);
            }
        } else {
            //template does not load
            $template = 'wirecard_error';
            $this->session->data['error'] = $this->language->get('error_init');
            $this->response->redirect($this->url->link('checkout/checkout', '', true));
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

    public function callback()
    {
        // Prefix
        $prefix = $this->prefix . $this->payment_type_prefix;

        // Load required files
        $this->load->model('checkout/order');
        $this->load->model('extension/payment/wirecard');

        $this->model_extension_payment_wirecard->write_log('confirm_request:' . print_r($_REQUEST, true));

        $message = null;
        if (! isset($_REQUEST['opencartOrderId']) || ! strlen($_REQUEST['opencartOrderId'])) {
            $message = 'order-id missing';
            $this->model_extension_payment_wirecard->write_log($message);
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

            // TODO
            if(is_array($v)) {
                $comment .= "$k:". print_r($v) . "\n";
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
            if (! $return->validate()) {
                $message = 'Validation error: invalid response';
                $this->model_checkout_order->addOrderHistory($order_id, $failureStatus);
                $this->model_checkout_order->write_log($message);
                return WirecardCEE_QPay_ReturnFactory::generateConfirmResponseString($message);
            }


            /**
             *
             * @var $return WirecardCEE_Stdlib_Return_ReturnAbstract
             */

            switch ($return->getPaymentState()) {
                case WirecardCEE_QPay_ReturnFactory::STATE_SUCCESS:
                    // for pending requests, confirm is already done, the model does only one
                    // confirm,
                    // so do an update in this case
                    if ($order['order_status_id']) {
                        $this->model_checkout_order->update($order_id, $successStatus, $comment, true);
                    } else {
                        $this->model_checkout_order->addOrderHistory($order_id, $successStatus, $comment, true);
                    }
                    break;

                case WirecardCEE_QPay_ReturnFactory::STATE_PENDING:
                    /**
                     *
                     * @var $return WirecardCEE_QPay_Return_Pending
                     */
                    $this->model_checkout_order->addOrderHistory($order_id, $pendingStatus, $comment, true);
                    break;

                // we can do nothing here, confirm() always sends the notification email
                // the update() method doese nothing if the order has not been confirmed yet
                // see catalog/model/checkout/order.php

                case WirecardCEE_QPay_ReturnFactory::STATE_CANCEL:
                    /**
                     *
                     * @var $return WirecardCEE_QPay_Return_Cancel
                     */
                    // $this->model_checkout_order->addOrderHistory($order_id, $cancelStatus);
                    // $this->model_checkout_order->update($order_id, $cancelStatus, $comment,
                    // false);
                    break;

                case WirecardCEE_QPay_ReturnFactory::STATE_FAILURE:
                    /**
                     *
                     * @var $return WirecardCEE_QPay_Return_Failure
                     */
                    // $this->model_checkout_order->addOrderHistory($order_id, $failureStatus, '',
                    // false);
                    //$this->model_checkout_order->update($order_id, $failureStatus, $return->getErrors()->getConsumerMessage(), false);

                    //$this->model_extension_payment_wirecard->write_log($e->getMessage());
                    $this->session->data['error'] = $return->getError()->getMessage();
                    break;

                default:
                    break;
            }
        } catch (Exception $e) {
            $this->model_checkout_order->addOrderHistory($order_id, $failureStatus);
            $this->model_extension_payment_wirecard->write_log($e->getMessage());
        }

        echo WirecardCEE_QPay_ReturnFactory::generateConfirmResponseString($message);
    }

    public function success()
    {
        $this->response->redirect($this->url->link('checkout/success', '', true));
        // if success redirect
        //echo '<script type="text/javascript"> parent.location ="' . $this->url->link('checkout/success') . '"</script>';
        //echo '<noscript>Javascript muss aktiviert sein</noscript>';
    }

    public function checkout()
    {
        $this->response->redirect($this->url->link('checkout/checkout', '', true));
        // if fail redirect
        //echo '<script type="text/javascript"> parent.location ="' . $this->url->link('checkout/checkout') . '"</script>';
        //echo 'Javascript muss aktiviert sein';
    }

    public function failure()
    {
        $this->response->redirect($this->url->link('checkout/failure', '', true));
        // if fail redirect
        //echo '<script type="text/javascript"> parent.location ="' . $this->url->link('checkout/checkout') . '"</script>';
        //echo 'Javascript muss aktiviert sein';
    }

}