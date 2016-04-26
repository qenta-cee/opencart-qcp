<?php
/*
 * Shop System Plugins - Terms of use This terms of use regulates warranty and liability between Wirecard Central
 * Eastern Europe (subsequently referred to as WDCEE) and it's contractual partners (subsequently referred to as
 * customer or customers) which are related to the use of plugins provided by WDCEE. The Plugin is provided by WDCEE
 * free of charge for it's customers and must be used for the purpose of WDCEE's payment platform integration only.
 * It explicitly is not part of the general contract between WDCEE and it's customer. The plugin has successfully been
 * tested under specific circumstances which are defined as the shopsystem's standard configuration (vendor's delivery
 * state). The Customer is responsible for testing the plugin's functionality before putting it into production enviroment.
 * The customer uses the plugin at own risk. WDCEE does not guarantee it's full functionality neither does WDCEE assume
 * liability for any disadvantage related to the use of this plugin. By installing the plugin into the shopsystem the
 * customer agrees to the terms of use. Please do not use this plugin if you do not agree to the terms of use!
 */

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . DIR_SYSTEM . '/library/wirecard');

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace("WirecardCEE");

class ControllerPaymentWirecard extends Controller
{
    protected $data = array();

    private $pluginVersion = '1.2.0';

    private $prefix = 'wirecard';

    const INVOICE_INSTALLMENT_MIN_AGE = 18;

    public function index()
    {
        // set Prefix
        $prefix = $this->prefix . $this->payment_type_prefix;

        // Load required files
        $this->load->model('checkout/order');
        $this->load->model('payment/wirecard');

        if ($prefix != 'wirecard') {
            $this->language->load('payment/wirecard');
        }

        $this->language->load('payment/' . $prefix);

        // additional Data
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['window_name'] = $this->model_payment_wirecard->get_window_name();

        // Set Template
        if ($this->payment_type == WirecardCEE_QPay_PaymentType::INSTALLMENT) {
            $template = 'wirecard_installment';
            $this->data['txt_info'] = $this->language->get('text_installment_info');
            $this->data['txt_birthday'] = $this->language->get('text_birthday');
        } elseif ($this->payment_type == WirecardCEE_QPay_PaymentType::INVOICE) {
            $template = 'wirecard_invoice';
            $this->data['txt_info'] = $this->language->get('text_invoice_info');
            $this->data['txt_birthday'] = $this->language->get('text_birthday');
        } else {
            $template = 'wirecard_init';
        }

        $this->data['send_order'] = $this->language->get('send_order');
        $this->data['error_init'] = $this->language->get('error_init');

        // Set Action URI
        $this->data['action'] = $this->url->link('payment/' . $prefix . '/init', '', 'SSL');

        // Template Output
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/' . $template . '.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/' . $template . '.tpl';
        } else {
            $this->template = 'default/template/payment/' . $template . '.tpl';
        }

        return $this->load->view($this->template, $this->data);
    }

    public function init()
    {
        if (!isset($_POST['wirecard_checkout_page_window_name']) || !isset($_SESSION['order_id'])) {
            $this->checkout();
        }

        $this->data['window_name'] = $_POST['wirecard_checkout_page_window_name'];

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

        // set Prefix
        $prefix = $this->prefix . $this->payment_type_prefix;

        // Load required files
        $this->load->model('checkout/order');
        $this->load->model('payment/wirecard');

        if ($prefix != 'wirecard') {
            $this->language->load('payment/wirecard');
        }

        $this->language->load('payment/' . $prefix);

        // check payment type
        if (isset($this->payment_type)) {
            $paymentType = $this->payment_type;
        } else {
            $paymentType = 'SELECT';
        }

        // additional Data
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $pluginVersion = WirecardCEE_QPay_FrontendClient::generatePluginVersion($order_info['store_name'], VERSION, 'Opencart Wirecard Checkout Page', $this->pluginVersion);

        // set fields, optional, comsumer, generate fingerprint, send request, redirect
        // user
        $result = $this->model_payment_wirecard->send_request($prefix, $paymentType, $order_info, $birthday, $pluginVersion);

        // If connection to wirecard success set template
        if ($result) {
            $this->data['action'] = $result;

            // Check if iframe is active
            if ($this->config->get($prefix . '_iframe') == '1') {
                $template = 'wirecard_iframe';
            } else {
                //// $template = 'wirecard';
                $this->response->redirect($this->data['action']);
            }
        } else {
            $template = 'wirecard_error';
        }

        // Template Output
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/' . $template . '.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/' . $template . '.tpl';
        } else {
            $this->template = 'default/template/payment/' . $template . '.tpl';
        }

        $this->data['header'] = $this->load->controller('common/header');
        $this->data['column_left'] = $this->load->controller('common/column_left');
        $this->data['column_right'] = $this->load->controller('common/column_right');
        $this->data['content_top'] = $this->load->controller('common/content_top');
        $this->data['content_bottom'] = $this->load->controller('common/content_bottom');
        $this->data['footer'] = $this->load->controller('common/footer');


        $this->response->setOutput($this->load->view($this->template, $this->data, $this->children));
    }

    public function callback()
    {
        // Prefix
        $prefix = $this->prefix . $this->payment_type_prefix;

        // Load required files
        $this->load->model('checkout/order');
        $this->load->model('payment/wirecard');

        $this->model_payment_wirecard->write_log('confirm_request:' . print_r($_REQUEST, true));

        $message = null;
        if (! isset($_REQUEST['opencartOrderId']) || ! strlen($_REQUEST['opencartOrderId'])) {
            $message = 'order-id missing';
            $this->model_payment_wirecard->write_log($message);
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
                    // $this->model_checkout_order->update($order_id, $failureStatus,
                    // $return->getErrors()->getConsumerMessage(), false);
                    break;

                default:
                    break;
            }
        } catch (Exception $e) {
            $this->model_checkout_order->addOrderHistory($order_id, $failureStatus);
            $this->model_payment_wirecard->write_log($e->getMessage());
        }

        echo WirecardCEE_QPay_ReturnFactory::generateConfirmResponseString($message);
    }

    public function success()
    {
        // if success redirect
        echo '<script type="text/javascript"> parent.location ="' . $this->url->link('checkout/success') . '"</script>';
        echo '<noscript>Javascript muss aktiviert sein</noscript>';
    }

    public function checkout()
    {
        // if fail redirect
        echo '<script type="text/javascript"> parent.location ="' . $this->url->link('checkout/checkout') . '"</script>';
        echo 'Javascript muss aktiviert sein';
    }
}
