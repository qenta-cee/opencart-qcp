<?php
/**
 * Shop System Plugins - Terms of Use
 *
 * The plugins offered are provided free of charge by QENTA Payment CEE GmbH
 * (abbreviated to QENTA) and are explicitly not part of the QENTA
 * CEE range of products and services.
 *
 * They have been tested and approved for full functionality in the standard
 * configuration
 * (status on delivery) of the corresponding shop system. They are under
 * General Public License Version 2 (GPLv2) and can be used, developed and
 * passed on to third parties under the same terms.
 *
 * However, QENTA does not provide any guarantee or accept any liability
 * for any errors occurring when used in an enhanced, customized shop system
 * configuration.
 *
 * Operation in an enhanced, customized configuration is at your own risk and
 * requires a comprehensive test phase by the user of the plugin.
 *
 * Customers use the plugins at their own risk. QENTA does not guarantee
 * their full functionality neither does QENTA assume liability for any
 * disadvantages related to the use of the plugins. Additionally, QENTA
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

class ControllerExtensionPaymentWirecardInstallment extends ControllerExtensionPaymentWirecard
{
    // define payment type
    public $payment_type = '_installment';

    // define input fields
    protected $arrayInputFields = array(
        'status' => 'undefinied',
        'customerId' => 'input',
        'secret' => 'input',
        'shopId' => 'input',
        'serviceUrl' => 'input',
        'backgroundColor' => 'input',
        'imageURL' => 'input',
        'displayText' => 'textarea',
        'success_status' => 'status_code',
        'pending_status' => 'status_code',
        'failure_status' => 'status_code',
        'cancel_status' => 'status_code',
        'autoDeposit' => 'true_false',
        'duplicateRequestCheck ' => 'true_false',
        'maxRetries' => 'input',
        'confirmMail' => 'input',
        'customerStatement' => 'textarea',
        'iframe' => 'true_false',
        'consumerInformation' => 'true_false',
        'basketData' => 'true_false',
        'provider' => 'select_provider',
        'terms' => 'true_false',
        'mId' => 'input',
        'minAmount' => 'input',
        'maxAmount' => 'input',
        'country' => 'select_country',
        'currency' => 'select_currency'
    );

    /**
     * @return bool
     *
     * validate form data and amount
     */
    protected function validate()
    {
        $boolHasValidationError = parent::validate();

        $fieldname = $this->prefix . $this->payment_type . '_minAmount';
        if (!empty($this->request->post[$fieldname]) and !is_numeric($this->request->post[$fieldname])) {
            $this->arrayInputFieldsMandatory['minAmount'] = true;
            $boolHasValidationError = false;
        }

        $fieldname = $this->prefix . $this->payment_type . '_maxAmount';
        if (!empty($this->request->post[$fieldname]) and !is_numeric($this->request->post[$fieldname])) {
            $this->arrayInputFieldsMandatory['maxAmount'] = true;
            $boolHasValidationError = false;
        }

        return $boolHasValidationError;
    }

}
