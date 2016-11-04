<?php

class ControllerExtensionPaymentWirecard extends Controller
{
    private $error = array();

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

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if ($this->validate($this->payment_type)) {
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

        $data['action'] = $this->url->link('extension/payment/' . $this->prefix . $this->payment_type, 'token=' . $this->session->data['token'],
            true);

        $data['cancel'] = $this->url->link('extension/extension',
            'token=' . $this->session->data['token'] . '&type=payment', true);


        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/wirecard', $data));
    }

    protected function validate($payment)
    {
        if (!$this->user->hasPermission('modify', 'extension/payment/wirecard')) {
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

    //not used yet
    protected function save()
    {
        // Save Post data
        $this->model_setting_setting->editSetting($this->prefix . $this->payment_type, $this->request->post);
        $this->session->data['success'] = $this->language->get('text_success');
        $this->response->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'],
            'SSL'));
    }
}
