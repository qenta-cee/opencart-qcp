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
class ControllerExtensionPaymentWirecard extends Controller
{
    private $error = array();

    // define plugin prefix
    protected $prefix = 'wirecard';

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
    );

    // define mandatory input fields, by false no error occur
    protected $arrayInputFieldsMandatory = array(
        'customerId' => false,
        'secret' => false,
        'serviceUrl' => false
    );

    /**
     * provided opencart function
     */
    public function index()
    {
        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');

        $this->load->language('extension/payment/wirecard');
        $this->load->language('extension/payment/' . $this->prefix . $this->payment_type);

        $data['boolHasValidationError'] = false;

        $this->document->setTitle($this->language->get('heading_title'));

        // Prefix
        $data['prefix'] = $this->prefix . $this->payment_type . '_';

        //save pluginsettings
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if ($this->validate()) {
                $this->model_setting_setting->editSetting($this->prefix . $this->payment_type, $this->request->post);

                $this->session->data['success'] = $this->language->get('text_success');

                $this->response->redirect($this->url->link('extension/extension',
                    'token=' . $this->session->data['token'] . '&type=payment', true));
            } else {
                $data['boolHasValidationError'] = true;
            }
        }

        // get Order statuses of Opencart
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        // get general language lines
        $data['arrayLanguageText']['heading_title'] = $this->language->get('heading_title');
        $data['arrayLanguageText']['text_edit'] = $this->language->get('text_edit');
        $data['arrayLanguageText']['button_save'] = $this->language->get('button_save');
        $data['arrayLanguageText']['button_cancel'] = $this->language->get('button_cancel');
        $data['arrayLanguageText']['text_enabled'] = $this->language->get('text_enabled');
        $data['arrayLanguageText']['text_disabled'] = $this->language->get('text_disabled');
        $data['arrayLanguageText']['enable_title'] = $this->language->get('enable_title');
        $data['arrayLanguageText']['enable_descript'] = $this->language->get('enable_descript');
        $data['arrayLanguageText']['field_name'] = $this->language->get('field_name');
        $data['arrayLanguageText']['no'] = $this->language->get('no');
        $data['arrayLanguageText']['yes'] = $this->language->get('yes');
        $data['arrayLanguageText']['license_text'] = $this->language->get('license_text');
	    $data['arrayLanguageText']['payolution'] = $this->language->get('payolution');
	    $data['arrayLanguageText']['wirecard'] = $this->language->get('wirecard');
	    $data['arrayLanguageText']['ratepay'] = $this->language->get('ratepay');

        //define input fields
        $data['inputfields'] = $this->arrayInputFields;
        // define Status
        $data['status'] = $this->prefix . $this->payment_type . '_status';
        $data['arrayInputFieldsMandatory'] = $this->arrayInputFieldsMandatory;

        // description language-texts
        foreach ($this->arrayInputFields as $key => $field) {
            $data['arrayLanguageText'][$key . '_descript'] = $this->language->get($key . '_descript');
        }

        // title inputfields language-texts
        foreach ($this->arrayInputFields as $key => $field) {
            $data['arrayLanguageText'][$key . '_title'] = $this->language->get($key . '_title');
        }

        // Error-Messages
        foreach ($data['arrayInputFieldsMandatory'] as $fieldname => $isError) {
            if ($isError) {
                $data['arrayErrorText'][$fieldname] = $this->language->get('error_' . $fieldname);
            }
        }

        // define field values
        foreach ($this->arrayInputFields as $fieldname => $fieldtype) {
            if (isset($this->request->post[$this->prefix . $fieldname])) {
                $data['input'][$data['prefix'] . $fieldname] = $this->request->post[$data['prefix'] . $fieldname];
            } else {
                $data['input'][$data['prefix'] . $fieldname] = $this->config->get($data['prefix'] . $fieldname);
            }
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/extension',
                'token=' . $this->session->data['token'] . '&type=payment', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/payment/wirecard', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/payment/' . $this->prefix . $this->payment_type,
            'token=' . $this->session->data['token'],
            true);

        $data['cancel'] = $this->url->link('extension/extension',
            'token=' . $this->session->data['token'] . '&type=payment', true);


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->load->model('localisation/country');
        $data['countries'] = $this->model_localisation_country->getCountries();
        $this->load->model('localisation/currency');
        $data['currencies'] = $this->model_localisation_currency->getCurrencies();

        $this->response->setOutput($this->load->view('extension/payment/wirecard', $data));
    }

    /**
     * @return bool
     *
     * validate required fields of form data
     */
    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/'. $this->prefix . $this->payment_type)) {
            $this->error['warning'] = $this->language->get('error_permission');
            return false;
        }
        $boolHasValidationError = false;

        foreach ($this->arrayInputFieldsMandatory as $fieldname => $value) {
            $temp_fieldname = $this->prefix . $this->payment_type . "_" . $fieldname;
            if (!$this->request->post[($temp_fieldname)]) {
                $boolHasValidationError = true;
                $this->arrayInputFieldsMandatory[$fieldname] = true;
            }
        }

        if ($boolHasValidationError === true) {
            return false;
        }

        return true;
    }

}
