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

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . DIR_SYSTEM . '/library/wirecard');

require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance()->registerNamespace("WirecardCEE");


class ModelPaymentWirecard extends Model {
    // define prefix
    protected $prefix = 'wirecard';

    // define fields for foreach
    // call : set_fields
    private $fields = array(
        'secret',
        'customerId',
        'backgroundColor',
        'displayText',
        'imageURL',
        'shopId',
        'serviceUrl'
    );

    const WINDOW_NAME = 'WirecardCheckoutPageFrame';

    // get payment method
    public function getMethod($address, $total) {

        $prefix = $this->prefix . $this->payment_type;

        $this->load->language('payment/' . $prefix);

        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get(
                $prefix . '_geo_zone_id'
            ) . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')"
        );

        if ($this->config->get($prefix . '_total') > $total) {
            $status = false;
        } elseif (!$this->config->get($prefix . '_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code' => $prefix,
                'title' => $this->language->get('text_title'),
                'terms' => '',
                'sort_order' => $this->config->get($prefix . '_sort_order')
            );
        }

        return $method_data;
    }

    /**
     * Get config values
     *
     * @param $prefix
     * @return mixed
     */
    public function get_config($prefix) {
        // set defined fields
        foreach ($this->fields as $field) {
            $data[$field] = $this->config->get($prefix . '_' . $field);
        }

        $data['shopId'] = null;
        if ($this->config->get($prefix . '_shopId') != '') {
            $data['shopId'] = $this->config->get($prefix . '_shopId');
        }

        $data['duplicateRequestCheck'] = $this->config->get($prefix . '_duplicateRequestCheck') == '1';

        $data['autoDeposit'] = $this->config->get($prefix . '_autoDeposit') == '1';

        $data['maxRetries'] = -1;
        if ($this->config->get($prefix . '_maxRetries') != '') {
            $data['maxRetries'] = $this->config->get($prefix . '_maxRetries');
        }

        $data['confirmMail'] = null;
        if ($this->config->get($prefix . '_confirmMail') != '') {
            $data['confirmMail'] = $this->config->get($prefix . '_confirmMail');
        }

        $data['sendConsumerInformation'] = $this->config->get($prefix . '_consumerInformation') == '1';

        return $data;
    }

    // set consumer data
    public function set_consumer_information($order, WirecardCEE_Stdlib_ConsumerData $consumer_data) {

        $consumer_data->setEmail($order['email']);

        $billingAddress = new WirecardCEE_Stdlib_ConsumerData_Address(WirecardCEE_Stdlib_ConsumerData_Address::TYPE_BILLING);

        $countryCode = $order['payment_iso_code_2'];
        $billingAddress->setFirstname($order['payment_firstname'])
            ->setLastname($order['payment_lastname'])
            ->setAddress1($order['payment_address_1'])
            ->setAddress2($order['payment_address_2'])
            ->setCity($order['payment_city'])
            ->setZipCode($order['payment_postcode'])
            ->setCountry($countryCode)
            ->setPhone($order['telephone'])
            ->setFax($order['fax']);

        if ($countryCode == 'US' || $countryCode == 'CA') {
            $billingAddress->setState(substr($order['payment_zone_code'], 0, 2));
        } else {
            $billingAddress->setState($order['payment_zone']);
        }

        $shippingAddress = new WirecardCEE_Stdlib_ConsumerData_Address(WirecardCEE_Stdlib_ConsumerData_Address::TYPE_SHIPPING);

        if (!empty($order['shipping_firstname']) && !empty($order['shipping_lastname']) && !empty($order['shipping_lastname'])) {
            $countryCode = $order['shipping_iso_code_2'];

            $shippingAddress->setFirstname($order['shipping_firstname'])
                ->setLastname($order['shipping_lastname'])
                ->setAddress1($order['shipping_address_1'])
                ->setAddress2($order['shipping_address_2'])
                ->setCity($order['shipping_city'])
                ->setZipCode($order['shipping_postcode'])
                ->setCountry($countryCode)
                ->setPhone($order['telephone'])
                ->setFax($order['fax']);

        } else {
            $shippingAddress->setFirstname($order['payment_firstname'])
                ->setLastname($order['payment_lastname'])
                ->setAddress1($order['payment_address_1'])
                ->setAddress2($order['payment_address_2'])
                ->setCity($order['payment_city'])
                ->setZipCode($order['payment_postcode'])
                ->setCountry($countryCode)
                ->setPhone($order['telephone'])
                ->setFax($order['fax']);
        }

        if ($countryCode == 'US' || $countryCode == 'CA') {
            $shippingAddress->setState(substr($order['shipping_zone_code'], 0, 2));
        } else {
            $shippingAddress->setState($order['shipping_zone']);
        }

        $consumer_data->addAddressInformation($billingAddress)
            ->addAddressInformation($shippingAddress);
    }

    public function send_request($prefix, $paymentType, $order, $birthday, $plugin_version) {
        $fields = $this->get_config($prefix);
        try {

            $client = new WirecardCEE_QPay_FrontendClient(array(
                'CUSTOMER_ID' => $fields['customerId'],
                'SHOP_ID' => $fields['shopId'],
                'SECRET' => $fields['secret'],
                'LANGUAGE' => $order['language_code']
            ));

            // consumer data (IP and User agent) are mandatory!
            $consumerData = new WirecardCEE_Stdlib_ConsumerData();
            $consumerData->setUserAgent($_SERVER['HTTP_USER_AGENT'])
                ->setIpAddress($_SERVER['REMOTE_ADDR']);

            if ($birthday !== null)
                $consumerData->setBirthDate($birthday);

            if ($fields['sendConsumerInformation'] || in_array(
                    $paymentType,
                    Array(WirecardCEE_QPay_PaymentType::INVOICE, WirecardCEE_QPay_PaymentType::INSTALLMENT)
                )
            ) {
                $this->set_consumer_information($order, $consumerData);
            }

            $strCustomerLayout = $this->get_customer_layout();

            $client
                ->setAmount(
                    $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false)
                )
                ->setCurrency($order['currency_code'])
                ->setPaymentType($paymentType)
                ->setOrderDescription($this->get_order_description($order))
                ->setPluginVersion($plugin_version)
                ->setSuccessUrl($this->url->link('payment/' . $prefix . '/success', null, 'SSL'))
                ->setPendingUrl($this->url->link('payment/' . $prefix . '/success', null, 'SSL'))
                ->setCancelUrl($this->url->link('payment/' . $prefix . '/checkout', null, 'SSL'))
                ->setFailureUrl($this->url->link('payment/' . $prefix . '/checkout', null, 'SSL'))
                ->setConfirmUrl($this->url->link('payment/' . $prefix . '/callback', null, 'SSL'))
                ->setServiceUrl($fields['serviceUrl'])
                ->setImageUrl($fields['imageURL'])
                ->setConsumerData($consumerData)
                ->setDisplayText($fields['displayText'])
                ->setCustomerStatement($this->get_customer_statement($order))
                ->setDuplicateRequestCheck(false)
                ->setMaxRetries($fields['maxRetries'])
                ->setAutoDeposit($fields['autoDeposit'])
                ->setWindowName($this->get_window_name())
                ->setCustomerLayout($strCustomerLayout);

            $client->opencartOrderId = $order['order_id'];

            $this->write_log(__METHOD__ . "\n" . print_r($client->getRequestData(), true));

            $response = $client->initiate();

            if ($response->hasFailed()) {
                $this->write_log("Response failed! Error: {$response->getError()->getMessage()}");
                return false;
            }
        } catch (Exception $e) {
            throw($e);
        }

        return $response->getRedirectUrl();
    }

    public function write_log($message) {
        $date = date("Y-m-d");
        $log_path = DIR_SYSTEM . 'logs/';
        $log_file = 'wirecard_log_' . $date . '.txt';
        $handle = fopen($log_path . $log_file, 'a+');
        fwrite($handle, $message . "\n");
        fclose($handle);
    }

    public function get_window_name() {
        return self::WINDOW_NAME;
    }

    protected function get_order_description($order) {
        return sprintf('user_id:%s order_id:%s', $order['customer_id'], $order['order_id']);
    }

    protected function get_customer_statement($order) {
        return sprintf('%s #%06s', $order['store_name'], $order['order_id']);
    }

    protected function get_customer_layout() {
        $objMobileDetect = new WirecardCEE_Stdlib_Mobiledetect();
        $layout = "desktop";

        if ($objMobileDetect->isMobile($_SERVER['HTTP_USER_AGENT']) === true) {
            $layout = "smartphone";
        }

        if ($objMobileDetect->isTablet($_SERVER['HTTP_USER_AGENT']) === true) {
            $layout = "tablet";
        }

        return $layout;
    }
}

