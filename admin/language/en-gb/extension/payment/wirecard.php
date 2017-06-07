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

// Breadcrumb
$_['text_payment'] = 'Payment';

// Page Title
$_['heading_title'] = 'Wirecard Checkout Page Select';
$_['text_edit'] = 'Edit';
$_['text_wirecard'] = '<a href="http://www.wirecard.at/" target="_blank"><img src="view/image/payment/wirecard.png" alt="Wirecard" title="Wirecard CEE" /></a>';

// Messages  & License text
$_['text_success'] = 'You have successfully modified the Wirecard account details';
$_['error_secret'] = 'Secret is required';
$_['error_customerId'] = 'Customer ID is required';
$_['error_serviceUrl'] = 'Service URL is required';
$_['error_minAmount'] = 'Min. Amount is invalid';
$_['error_maxAmount'] = 'Max. Amount is invalid';
$_['license_text'] = "<h1>Terms of use	</h1> This terms of use regulates warranty and liability between Wirecard Central Eastern Europe (subsequently referred to as WDCEE) and it's	 contractual partners (subsequently referred to as customer or customers) which are related to the use of plugins provided by WDCEE.	 The Plugin is provided by WDCEE free of charge for it's customers and must be used for the purpose of WDCEE's payment platform	 integration only. It explicitly is not part of the general contract between WDCEE and it's customer. The plugin has successfully been tested	 under specific circumstances which are defined as the shopsystem's standard configuration (vendor's delivery state). The Customer is	 responsible for testing the plugin's functionality before putting it into production enviroment.	 The customer uses the plugin at own risk. WDCEE does not guarantee it's full functionality neither does WDCEE assume liability for any	 disadvantage related to the use of this plugin. By installing the plugin into the shopsystem the customer agrees to the terms of use.	 Please do not use this plugin if you do not agree to the terms of use!";

// Field titles
$_['enable_title'] = 'Enable';
$_['customerId_title'] = 'Customer ID';
$_['secret_title'] = 'Secret';
$_['serviceUrl_title'] = 'Service Url';
$_['currency_title'] = 'Currency';
$_['language_title'] = 'Language';
$_['imageURL_title'] = 'Logo Url';
$_['minAmount_title'] = 'Min. Amount';
$_['maxAmount_title'] = 'Max. Amount';
$_['success_status_title'] = 'Success Status';
$_['pending_status_title'] = 'Pending Status';
$_['failure_status_title'] = 'Failure Status';
$_['cancel_status_title'] = 'Cancel Status';
$_['shopId_title'] = 'Shop ID';
$_['backgroundColor_title'] = 'Background Color';
$_['displayText_title'] = 'Display Text';

// Field Descriptions
$_['enable_descript'] = 'Enable the plugin?';
$_['customerId_descript'] = 'The  &quot;Customer ID &quot; you received from Wirecard ';
$_['secret_descript'] = 'The  &quot;Secrect&quot; you received from Wirecard';
$_['serviceUrl_descript'] = 'Service Url, in most cases the link to the imprint';
$_['currency_descript'] = 'Currency for the payment page';
$_['language_descript'] = 'Language of the payment page';
$_['imageURL_descript'] = 'Displays your logo on payment page';
$_['minAmount_descript'] = 'Min. Amount';
$_['maxAmount_descript'] = 'Max. Amount';
$_['success_status_descript'] = 'Order status for payment status SUCCESS';
$_['pending_status_descript'] = 'Order status for payment status PENDING';
$_['failure_status_descript'] = 'Order status for payment status FAILURE';
$_['cancel_status_descript'] = 'Order status for payment status CANCEL';
$_['shopId_descript'] = 'Your “Shop ID“ you revceived from Wirecard';
$_['backgroundColor_descript'] = 'Set a background color for the payment page';
$_['displayText_descript'] = 'This text will be displayed on the payment page';

$_['autoDeposit_title'] = 'Auto Deposit';
$_['autoDeposit_descript'] = 'If this option is set to &quot;active&quot; the booking will be done immediately after successful payment.';

$_['duplicateRequestCheck _title'] = 'Duplicate Request Check';
$_['duplicateRequestCheck _descript'] = 'This parameter prevents accidental unintended multiple payments triggered by your customers';

$_['maxRetries_title'] = 'Max. Retries';
$_['maxRetries_descript'] = 'The parameter specifies the maximum number of payment attempts';

$_['confirmMail_title'] = 'Confirm E-Mail Address';
$_['confirmMail_descript'] = 'If you use this parameter you receive an e-mail which contains all payment information such as order number, payment and payment amount';

$_['customerStatement_title'] = 'Customer Statement';
$_['customerStatement_descript'] = 'This text appears on the billing of customers';

$_['iframe_title'] = 'iFrame';
$_['iframe_descript'] = 'Should the payment page be displayed in an iframe?';

$_['consumerInformation_title'] = 'Forward consumer data';
$_['consumerInformation_descript'] = 'Forwarding shipping and billing data about your consumer to the respective financial service provider.';

$_['no'] = 'Inactive';
$_['yes'] = 'Active';

$_['basketData_title'] = 'Forward basket data';
$_['basketData_descript'] = 'Forwarding basket data to the respective financial service provider.';

$_['payolution'] = 'payolution';
$_['wirecard'] = 'Wirecard';
$_['ratepay'] = 'RatePay';

$_['all_countries'] = 'All countries';
$_['all_currencies'] = 'All currencies';