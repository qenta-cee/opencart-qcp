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

ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . DIR_SYSTEM . '/library/qenta');

require_once 'vendor/autoload.php';

use \QentaCEE\QPay\FrontendClient as QentaFrontendClient;
use \QentaCEE\QPay\PaymentType;
use \QentaCEE\Stdlib\Basket as QentaBasket;
use \QentaCEE\Stdlib\ConsumerData as QentaConsumerData;

class ModelExtensionPaymentQenta extends Model
{
    // define prefix
    protected $prefix = 'qenta';

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

    const WINDOW_NAME = 'QentaCheckoutPageFrame';

    /**
     * @param $address
     * @param $total
     * @return array
     */
    public function getMethod($address, $total)
    {
        $prefix = $this->prefix . $this->payment_type;

        $this->load->language('extension/payment/' . $prefix);

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_' . $prefix . '_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('payment_' . $prefix . '_total') > 0 && $this->config->get('payment_' . $prefix . '_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('payment_' . $prefix . '_geo_zone_id')) {
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
                'sort_order' => $this->config->get('payment_' . $prefix . '_sort_order')
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
    public function getConfig($prefix)
    {
        $prefix = 'payment_' . $prefix;
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
        $data['sendBasketData'] = $this->config->get($prefix . '_basketData') == '1';

        return $data;
    }

    /**
     * @param $order
     * @param QentaConsumerData\ConsumerData $consumer_data
     *
     * set consumerdata
     */
    public function setConsumerInformation($order, QentaConsumerData $consumer_data)
    {

        $consumer_data->setEmail($order['email']);

        $billingAddress = new QentaConsumerData\Address(QentaConsumerData\Address::TYPE_BILLING);

        $countryCode = $order['payment_iso_code_2'];
        $billingAddress->setFirstname($order['payment_firstname'])
            ->setLastname($order['payment_lastname'])
            ->setAddress1($order['payment_address_1'])
            ->setAddress2($order['payment_address_2'])
            ->setCity($order['payment_city'])
            ->setZipCode($order['payment_postcode'])
            ->setCountry($countryCode)
            ->setPhone($order['telephone']);

        if ($countryCode == 'US' || $countryCode == 'CA') {
            $billingAddress->setState(substr($order['payment_zone_code'], 0, 2));
        } else {
            $billingAddress->setState($order['payment_zone']);
        }

        $shippingAddress = new QentaConsumerData\Address(QentaConsumerData\Address::TYPE_SHIPPING);

        if (!empty($order['shipping_firstname']) && !empty($order['shipping_lastname']) && !empty($order['shipping_lastname'])) {
            $countryCode = $order['shipping_iso_code_2'];

            $shippingAddress->setFirstname($order['shipping_firstname'])
                ->setLastname($order['shipping_lastname'])
                ->setAddress1($order['shipping_address_1'])
                ->setAddress2($order['shipping_address_2'])
                ->setCity($order['shipping_city'])
                ->setZipCode($order['shipping_postcode'])
                ->setCountry($countryCode);

        } else {
            $shippingAddress->setFirstname($order['payment_firstname'])
                ->setLastname($order['payment_lastname'])
                ->setAddress1($order['payment_address_1'])
                ->setAddress2($order['payment_address_2'])
                ->setCity($order['payment_city'])
                ->setZipCode($order['payment_postcode'])
                ->setCountry($countryCode)
                ->setPhone($order['telephone']);
        }

        if ($countryCode == 'US' || $countryCode == 'CA') {
            $shippingAddress->setState(substr($order['shipping_zone_code'], 0, 2));
        } else {
            $shippingAddress->setState($order['shipping_zone']);
        }

        $consumer_data->addAddressInformation($billingAddress)
            ->addAddressInformation($shippingAddress);
    }

    /**
     * @param $prefix
     * @param $paymentType
     * @param $order
     * @param $birthday
     * @param $plugin_version
     * @return bool|string
     * @throws Exception
     *
     * send payment request
     */
    public function sendRequest($prefix, $paymentType, $order, $birthday, $plugin_version, $financialinstitution)
    {
        $fields = $this->getConfig($prefix);
        try {
            $language_info = $order['language_code'];
            //languagecode is like 'en-gb' but should be 'en'
            if (strpos($language_info, '-') !== false) {
                $language_info = explode('-', $language_info);
                $language_info = $language_info[0];
            }

            $client = new QentaFrontendClient(array(
                'CUSTOMER_ID' => $fields['customerId'],
                'SHOP_ID' => $fields['shopId'],
                'SECRET' => $fields['secret'],
                'LANGUAGE' => $language_info
            ));

            // consumer data (IP and User agent) are mandatory!
            $consumerData = new QentaConsumerData();
            $consumerData->setUserAgent($_SERVER['HTTP_USER_AGENT'])
                ->setIpAddress($_SERVER['REMOTE_ADDR']);

	        if ($birthday !== NULL) {
		        $consumerData->setBirthDate($birthday);
	        }

	        if ($financialinstitution !== NULL) {
		        $client->setFinancialInstitution($financialinstitution);
	        }

            if ($paymentType == PaymentType::MASTERPASS) {
                $client->setShippingProfile('NO_SHIPPING');
            }

            if ($fields['sendConsumerInformation'] || in_array(
                    $paymentType,
                    Array(PaymentType::INVOICE, PaymentType::INSTALLMENT)
                )
            ) {
                $this->setConsumerInformation($order, $consumerData);
            }

            $client
                ->setAmount(
                    $this->currency->format($order['total'], $order['currency_code'], $order['currency_value'], false)
                )
                ->setCurrency($order['currency_code'])
                ->setPaymentType($paymentType)
                ->setOrderDescription($this->getOrderDescription($order))
                ->setPluginVersion($plugin_version)
                ->setSuccessUrl($this->url->link('extension/payment/' . $prefix . '/success', null, 'SSL'))
                ->setPendingUrl($this->url->link('extension/payment/' . $prefix . '/success', null, 'SSL'))
                ->setCancelUrl($this->url->link('extension/payment/' . $prefix . '/checkout', null, 'SSL'))
                ->setFailureUrl($this->url->link('extension/payment/' . $prefix . '/failure', null, 'SSL'))
                ->setConfirmUrl($this->url->link('extension/payment/' . $prefix . '/callback', null, 'SSL'))
                ->setServiceUrl($fields['serviceUrl'])
                ->setImageUrl($fields['imageURL'])
                ->setConsumerData($consumerData)
                ->createConsumerMerchantCrmId($order['email'])
                ->setDisplayText($fields['displayText'])
                ->setCustomerStatement($this->getCustomerStatement($order, $paymentType, $prefix))
                ->setDuplicateRequestCheck(false)
                ->setMaxRetries($fields['maxRetries'])
                ->setAutoDeposit($fields['autoDeposit'])
                ->setWindowName($this->get_window_name())
                ->setOrderReference(md5($this->getOrderReference($order).microtime()));

            if (isset($_SESSION['qcpConsumerDeviceId'])) {
            	$client->consumerDeviceId = $_SESSION['qcpConsumerDeviceId'];
            	unset($_SESSION['qcpConsumerDeviceId']);
            }
	        if ($fields['sendBasketData'] ||
		        ($paymentType == PaymentType::INVOICE && $this->config->get('payment_'.$prefix.'_provider') != 'payolution') ||
		        ($paymentType == PaymentType::INSTALLMENT && $this->config->get('payment_'.$prefix.'_provider') != 'payolution')
	        ) {
		        $client->setBasket($this->setBasketData($order['currency_code'], $order['currency_value']));
	        }

            $client->opencartOrderId = $order['order_id'];

            $this->writeLog(__METHOD__ . "\n" . print_r($client->getRequestData(), true));

            $response = $client->initiate();

            if ($response->hasFailed()) {
                $this->writeLog("Response failed! Error: {$response->getError()->getMessage()}");
                return $response->getError();
            }
        } catch (Exception $e) {
            throw($e);
        }

        return $response->getRedirectUrl();
    }

	/**
	 * Create basket items including shipping and fix taxes
	 *
	 * @return QentaBasket
	 */
	public function setBasketData($currencyCode, $currencyValue) {
		$basket        = new QentaBasket();
		$basketContent = $this->cart;

		$fix_tax = 0;
		foreach ($basketContent->getProducts() as $cart_item_key => $cart_item) {
			$item         = new QentaBasket\Item($cart_item['product_id']);
            $gross_amount = $this->tax->calculate($this->convert($cart_item['price'], $currencyCode, $currencyValue), $cart_item['tax_class_id'], 'P');
			$tax_amount   = $gross_amount - $this->convert($cart_item['price'], $currencyCode, $currencyValue);
			$item->setUnitGrossAmount($gross_amount)
				->setUnitNetAmount($this->convert($cart_item['price'], $currencyCode, $currencyValue))
				->setUnitTaxAmount($tax_amount)
				->setUnitTaxRate($this->convert($tax_amount / $cart_item['price'] * 100, $currencyCode, $currencyValue))
				->setUnitTaxRate($this->convert($tax_amount / $cart_item['price'] * 100, $currencyCode, $currencyValue))
				->setDescription(substr(utf8_decode($cart_item['name']), 0, 127))
				->setName(substr(utf8_decode($cart_item['name']), 0, 127))
				->setImageUrl($this->url->link($cart_item['image']));

			$basket->addItem($item, $cart_item['quantity']);
            $tax = $this->tax->calculate($this->convert($cart_item['price'], $currencyCode, $currencyValue), $cart_item['tax_class_id'], 'F') - $this->convert($cart_item['price'], $currencyCode, $currencyValue);
            $fix_tax += $tax * $cart_item['quantity'];
        }
		//Add shipping to basket
		if (isset($this->session->data['shipping_method'])) {
			$session_data    = $this->session->data;
			$shipping_method = $session_data['shipping_method'];
			$item            = new QentaBasket\Item('shipping');
			$item->setUnitGrossAmount($this->convertAndFormat($this->tax->calculate($shipping_method['cost'], $shipping_method['tax_class_id'], 'P'), $currencyCode, $currencyValue))
				->setUnitNetAmount($this->convertAndFormat($shipping_method['cost'], $currencyCode, $currencyValue))
				->setUnitTaxRate($this->convert($this->tax->getTax($shipping_method['cost'], $shipping_method['tax_class_id']) / $shipping_method['cost'] * 100, $currencyCode, $currencyValue))
				->setUnitTaxAmount($this->convertAndFormat($this->tax->getTax($shipping_method['cost'], $shipping_method['tax_class_id']), $currencyCode, $currencyValue))
				->setName('Shipping')
				->setDescription('Shipping');
			$basket->addItem($item);
			$fix_tax += $this->tax->calculate($shipping_method['cost'], $shipping_method['tax_class_id'], 'F') - $shipping_method['cost'];
		}

		// Add fix tax as basket item
		if ($fix_tax > 0) {
			$item = new QentaBasket\Item('FixTax');
			$item->setUnitGrossAmount($this->convertAndFormat($fix_tax, $currencyCode, $currencyValue))
				->setUnitNetAmount($this->convertAndFormat($fix_tax, $currencyCode, $currencyValue))
				->setUnitTaxAmount($this->convertAndFormat(0, $currencyCode, $currencyValue))
				->setUnitTaxRate(0)
				->setDescription('FixTax')
				->setName('FixTax');

			$basket->addItem($item, 1);
		}
		return $basket;
	}

    /**
     * @param $message
     *
     * write to logfile
     */
    public function writeLog($message)
    {
        $date = date("Y-m-d");
        $log_path = DIR_SYSTEM . 'storage/logs/';
        $log_file = 'qenta_log_' . $date . '.txt';
        $handle = fopen($log_path . $log_file, 'a+');
        fwrite($handle, $message . "\n");
        fclose($handle);
    }

    /**
     * @return string
     */
    public function get_window_name()
    {
        return self::WINDOW_NAME;
    }

    /**
     * @param $order
     * @return string
     */
    protected function getOrderDescription($order)
    {
	    return sprintf('%s %s %s',
		    $order['email'],
		    $order['payment_firstname'],
		    $order['payment_lastname']
	    );
    }

    /**
     * @param $order
     * @return string
     */
    protected function getCustomerStatement($order, $payment_type, $prefix)
    {
    	if(strlen($this->config->get('payment_'.$prefix.'_customerStatement'))) {
    		return $this->config->get('payment_'.$prefix.'_customerStatement');
	    }
        $customer_statement = sprintf('%9s', substr($order['store_name'], 0, 9));
	    if ($payment_type != PaymentType::POLI) {
		    $customer_statement .= ' ' . $this->getOrderReference($order);
	    }
	    return $customer_statement;
    }

	/**
	 * @param $order
	 *
	 * @return string
	 */
    protected function getOrderReference($order)
    {
    	return sprintf('%010s', substr($order['order_id'], -10));
    }

    /**
     * @param $amount
     * @param $currencyCode
     * @param $currencyValue
     *
     * @return float
     */
    protected function convertAndFormat($amount, $currencyCode, $currencyValue)
    {
        return number_format($this->currency->format($amount, $currencyCode, $currencyValue, false), 2);
    }

    /**
     * @param $amount
     * @param $currencyCode
     * @param $currencyValue
     *
     * @return float
     */
    protected function convert($amount, $currencyCode, $currencyValue)
    {
        return $this->currency->format($amount, $currencyCode, $currencyValue, false);
    }
}
