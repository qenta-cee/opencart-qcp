<?php
/*
 * Shop System Plugins - Terms of use
 *
 * This terms of use regulates warranty and liability between Wirecard Central Eastern Europe (subsequently referred to as WDCEE) and it's
 * contractual partners (subsequently referred to as customer or customers) which are related to the use of plugins provided by WDCEE.
 *
 * The Plugin is provided by WDCEE free of charge for it's customers and must be used for the purpose of WDCEE's payment platform
 * integration only. It explicitly is not part of the general contract between WDCEE and it's customer. The plugin has successfully been tested
 * under specific circumstances which are defined as the shopsystem's standard configuration (vendor's delivery state). The Customer is
 * responsible for testing the plugin's functionality before putting it into production enviroment.
 * The customer uses the plugin at own risk. WDCEE does not guarantee it's full functionality neither does WDCEE assume liability for any
 * disadvantage related to the use of this plugin. By installing the plugin into the shopsystem the customer agrees to the terms of use.
 * Please do not use this plugin if you do not agree to the terms of use!
 */

/*
catalog/controller/payment/moneybrace.php (Controller class)
catalog/model/payment/moneybrace.php  (Model class)
catalog/language/english/payment/moneybrace.php (Language file for View)
catalog/view/theme/default/template/payment/moneybrace.tpl (Template file for View)
admin/controller/payment/moneybrace.php
admin/language/english/payment/moneybrace.php
admin/view/template/payment/moneybrace.php
*/

class ControllerPaymentWirecard extends Controller {
    // define prefix
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
        'consumerInformation' => 'true_false'
    );

    // define mandatory input fields, by false no error occur
    protected $arrayInputFieldsMandatory = array(
        'customerId' => false,
        'secret' => false,
        'serviceUrl' => false
    );

    public function index() {
        // load required models and language files
        $this->load->model('setting/setting');
        $this->load->model('localisation/order_status');
        $this->load->language('payment/wirecard');
        $this->load->language('payment/' . $this->prefix . $this->payment_type);

        $data['boolHasValidationError'] = false;
        // Save the POST data
        if (($this->request->server['REQUEST_METHOD'] === 'POST')) {
            if ($this->validate()) {
                $this->save();
            }
            else {
                $data['boolHasValidationError'] = true;
            }
        }

        // ################################# frontend-data #############################################################
        // Prefix
        $data['prefix'] = $this->prefix . $this->payment_type . '_';

        // get Order statuses of Opencart
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        // define urls for submit and cancel
        $data['action'] = $this->url->link(
            'payment/' . $this->prefix . $this->payment_type,
            'token=' . $this->session->data['token'],
            'SSL'
        );

        $data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        // define input fields
        $data['inputfields'] = $this->arrayInputFields;

        // define Status
        $data['status'] = $this->prefix . $this->payment_type . '_status';

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/' . $this->prefix . $this->payment_type, 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['arrayInputFieldsMandatory'] = $this->arrayInputFieldsMandatory;

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

        // description language-texts
        foreach ($this->arrayInputFields as $key => $field) {
            $data['arrayLanguageText'][$key . '_descript'] = $this->language->get($key . '_descript');
        }

        // title inputfields language-texts
        foreach ($this->arrayInputFields as $key => $field) {
            $data['arrayLanguageText'][$key . '_title'] = $this->language->get($key . '_title');
        }

        // Error-Messages
        foreach($data['arrayInputFieldsMandatory'] as $fieldname => $isError) {
            if($isError) {
                $data['arrayErrorText'][$fieldname] = $this->language->get('error_' . $fieldname);
            }
        }
        // #############################################################################################################

        // Set document title
        $this->document->setTitle($this->language->get('heading_title'));

        // define field values
        foreach ($this->arrayInputFields as $fieldname => $fieldtype) {
            if (isset($this->request->post[$data['prefix'] . $fieldname])) {
                $data['input'][$data['prefix'] . $fieldname] = $this->request->post[$data['prefix'] . $fieldname];
            } else {
                $data['input'][$data['prefix'] . $fieldname] = $this->config->get($data['prefix'] . $fieldname);
            }
        }

        // Template output siteparts
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('payment/wirecard.tpl', $data));
    }

    protected function validate() {
        $boolHasValidationError = false;

        foreach ($this->arrayInputFieldsMandatory as $fieldname => $value) {
            if (!$this->request->post[$this->prefix . $this->payment_type . "_" . $fieldname]) {
                $boolHasValidationError = true;
                $this->arrayInputFieldsMandatory[$fieldname] = true;
            }
        }

        if ($boolHasValidationError === true) {
            return false;
        } else {
            return true;
        }
    }

    protected function save() {
        // Save Post data
        $this->model_setting_setting->editSetting($this->prefix . $this->payment_type, $this->request->post);
        $this->session->data['success'] = $this->language->get('text_success');
        $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
    }

}
