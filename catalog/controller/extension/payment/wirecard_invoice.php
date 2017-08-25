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

// Load main controller
$dir = dirname(__FILE__);
require_once($dir . '/wirecard.php');

class ControllerExtensionPaymentWirecardInvoice extends ControllerExtensionPaymentWirecard
{
    public $payment_type_prefix = '_invoice';
    public $payment_type = WirecardCEE_QPay_PaymentType::INVOICE;

	public function index() {
		$prefix = 'wirecard'.$this->payment_type_prefix;

		// Load required files
		$this->load->model('checkout/order');
		$this->load->model('extension/payment/wirecard');

		$this->load->language('extension/payment/wirecard');
		$this->load->language('extension/payment/'.$prefix);

		// additional Data
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['window_name']    = $this->model_extension_payment_wirecard->get_window_name();

		$template                          = 'wirecard_invoice';
		$data['provider']                  = $this->config->get($prefix.'_provider');
		$data['terms']                     = $this->config->get($prefix.'_terms');
		$data['mId']                       = $this->config->get($prefix.'_mId');
		$data['text_title']                = $this->language->get('text_title');
		$data['text_birthday']             = $this->language->get('text_birthday');
		$data['text_birthday_information'] = $this->language->get('text_birthday_information');
		$data['text_payolution_title']     = $this->language->get('text_payolution_title');
		$data['text_payolution_consent1']  = $this->language->get('text_payolution_consent1');
		$data['text_payolution_consent2']  = $this->language->get('text_payolution_consent2');
		$data['text_payolution_link']      = $this->language->get('text_payolution_link');

		$data['years']  = range(date('Y'), date('Y') - 100);
		$data['days']   = range(1, 31);
		$data['months'] = range(1, 12);

		$data['send_order'] = $this->language->get('send_order');
		$data['error_init'] = $this->language->get('error_init');

		$data['wcp_ratepay'] = $this->loadRatePay();
		// Set Action URI
		$data['action'] = $this->url->link('extension/payment/'.$prefix.'/init', '', 'SSL');

		// Template Output
		if (file_exists(DIR_TEMPLATE.$this->config->get('config_template').'/template/extension/payment/'.$template.'.tpl')) {
			$this->template = $this->config->get('config_template').'/template/extension/payment/'.$template.'.tpl';
		} else {
			$this->template = 'extension/payment/'.$template.'.tpl';
		}

		return $this->load->view($this->template, $data);
	}
}
