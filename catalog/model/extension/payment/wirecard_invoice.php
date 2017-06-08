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

class ModelExtensionPaymentWirecardInvoice extends ModelExtensionPaymentWirecard
{
    public $payment_type = '_invoice';

    /**
     * @param $address
     * @param $total
     * @return array|bool
     *
     * get payment method
     */
    public function getMethod($address, $total)
    {
        if ($this->cart->hasShipping()) {
            $this->load->model('account/address');

            $payment_address = $this->model_account_address->getAddress($this->session->data['payment_address']['address_id']);
            $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address']['address_id']);
            $fields = array(
                'firstname',
                'lastname',
                'company',
                'address_1',
                'address_2',
                'postcode',
                'city',
                'zone',
                'country'
            );
            foreach ($fields as $field) {
                if ($payment_address[$field] != $shipping_address[$field]) {
                    return false;
                }
            }
        }

	    $min_amount = $this->config->get($this->prefix.$this->payment_type.'_minAmount');
	    if ( ! empty($min_amount) && $total < $min_amount) {
		    return FALSE;
	    }

	    $max_amount = $this->config->get($this->prefix.$this->payment_type.'_maxAmount');
	    if ( ! empty($max_amount) && $total > $max_amount) {
		    return FALSE;
	    }

	    $currencies = $this->config->get($this->prefix.$this->payment_type.'_currency');
	    if ( ! empty($currencies) && ! in_array($this->session->data['currency'], $currencies)) {
		    return FALSE;
	    }
	    $country_ids = $this->config->get($this->prefix.$this->payment_type.'_country');
	    if ( ! empty($country_ids) && ! in_array($this->session->data['payment_address']['country_id'], $country_ids)) {
		    return FALSE;
	    }

        return parent::getMethod($address, $total);
    }
}
