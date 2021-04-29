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

// Breadcrumb
$_['text_payment'] = 'Bezahlung';

// Page Title
$_['heading_title'] = 'QENTA Checkout Page Select';
$_['text_edit'] = 'Bearbeite';
$_['text_qenta'] = '<a href="http://www.qenta-cee.at/" target="_blank"><img src="view/image/payment/qenta.png" alt="QENTA" title="QENTA" /></a>';
$_['text_enabled'] = 'Aktiviert';
$_['text_disabled'] = 'Deaktiviert';

// Messages  & License text
$_['text_success'] = 'QENTA Details wurden erfolgreich aktualisiert';
$_['error_secret'] = 'Secret ist erforderlich';
$_['error_customerId'] = 'Customer ID ist erforderlich';
$_['error_serviceUrl'] = 'Eine Service URL ist erforderlich';
$_['error_minAmount'] = 'Min. Amount ist ung&uuml;ltig';
$_['error_maxAmount'] = 'Max. Amount ist ung&uuml;ltig';
$_['license_text'] = "<h1>Nutzungsbedingungen</h1>

Diese Vereinbarung regelt die Gew&auml;hreistung und Haftung zwischen QENTA Payment CEE(nachfolgend kurz QENTA) und seinen
Vertragspartnern (nachfolgend kurz Kunde oder Kunden) betreffend Verwendung von QENTA bereitgestellten Plugins.

Das Plugin wird kostenlos von QENTA f&uuml;r Kunden zur Verf&uuml;gung gestellt und darf ausschlie&szlig;lich f&uuml;r die Anbindung an die QENTA Payment
Plattform verwendet werden. Das Plugin z&auml;hlt ausdr&uuml;cklich nicht zum &uuml;blichen Leistungsumfang der QENTA betreffend Zahlungabwicklung.
Das Plugin wurde erfolgreich unter bestimmten Umst&auml;nden getestet die als Standardkonfiguration des Shopsystems (Hersteller
Auslieferungszustand des Shopsystems) definiert sind. Der Kunde ist verantwortlich, die einwandfreie Funktion des Plugins sicherzustellen,
bevor es produktiv verwendet wird.
Der Kunde verwendet das Plugin auf eigenes Risiko. QENTA &uuml;bernimmt keine Garantie f&uuml;r die einwandfreie Funktionalit&auml;t des Plugins.
Des weitern wird von QENTA keinerlei Haftung f&uuml;r Sch&auml;den &uuml;bernommen, die dem Kunden in Verbindung mit dem Einsatz des Plugins entstehen.
Mit der Installation des Plugins stimmt der Kunde diesen Nutzungsbedingungen zu.
Bitte verwenden Sie das Plugin nicht wenn Sie mit dieser Vereinbarung nicht einverstanden sind!";

// Field titles
$_['enable_title'] = 'Aktivieren';
$_['customerId_title'] = 'Customer ID';
$_['secret_title'] = 'Secret';
$_['serviceUrl_title'] = 'Service Url';
$_['currency_title'] = 'W&auml;hrung';
$_['language_title'] = 'Sprache';
$_['imageURL_title'] = 'Logo Url';
$_['minAmount_title'] = 'min. Betrag';
$_['maxAmount_title'] = 'max. Betrag';
$_['success_status_title'] = 'Erfolgreicher Status';
$_['pending_status_title'] = 'Ausstehender Status';
$_['failure_status_title'] = 'Fehlgeschlagener Status';
$_['cancel_status_title'] = 'Abgebrochener Status';
$_['shopId_title'] = 'Shop ID';
$_['backgroundColor_title'] = 'Hintergrundfarbe';
$_['displayText_title'] = 'Display Text';

// Field Descriptions
$_['enable_descript'] = 'Plugin aktivieren?';
$_['customerId_descript'] = 'Die "Customer ID" die Sie von QENTA erhalten haben';
$_['serviceUrl_descript'] = 'Service Url, für gewöhnlich der Link zum Impressum';
$_['secret_descript'] = 'Der "Secrect" den Sie von QENTA erhalten haben';
$_['currency_descript'] = 'W&auml;hrung der Bezahlseite';
$_['language_descript'] = 'Sprache der Bezahlseite';
$_['imageURL_descript'] = 'Stellen Sie Ihr Logo auf der Bezahlseite dar';
$_['minAmount_descript'] = 'Min. Betrag';
$_['maxAmount_descript'] = 'Max. Betrag';

$_['success_status_desc'] = 'Status f&uuml;r erfolgreiche Bezahlung';
$_['pending_status_desc'] = 'Status f&uuml;r ausstehende Bezahlung';
$_['failure_status_desc'] = 'Status f&uuml;r fehlgeschlagene Bezahlung';
$_['cancel_status_desc'] = 'Status f&uuml;r abgebrochene Bezahlung';

$_['shopId_descript'] = 'Die “Shop ID“ die Sie von QENTA erhalten haben';
$_['backgroundColor_descript'] = 'Hintergrundfarbe f&uuml;r die Bezahlseite';
$_['displayText_descript'] = 'Text der auf der Bezahlseite angezeigt werden soll';

$_['autoDeposit_title'] = 'Auto Abbuchung';
$_['autoDeposit_descript'] = 'Ist diese Option auf "aktiv" gesetzt so wird nach erfolgreicher Bezahlung die Buchung sofort durchgeführt';

$_['duplicateRequestCheck _title'] = 'Doppelte Anfrage prüfen';
$_['duplicateRequestCheck _descript'] = 'Dieser Parameter verhindert, dass versehentlich unbeabsichtigte Mehrfachzahlungen durch Ihre Kunden ausgelöst werden';


$_['maxRetries_title'] = 'Max. Zahlungsversuche';
$_['maxRetries_descript'] = 'Der Parameter gibt die maximale Anzahl der Zahlungsversuche an';

$_['confirmMail_title'] = 'Bestätigungs E-Mail Adresse';
$_['confirmMail_descript'] = 'Wenn Sie diesen Parameter verwenden, erhalten Sie ein E-Mail, das alle
Informationen zur Zahlung wie etwa Auftragsnummer, Zahlungsmittel, Zahlungsbetrag
enthält.';

$_['customerStatement_title'] = 'Kunden Statement';
$_['customerStatement_descript'] = 'Dieser Text erscheint auf der Abrechnung des Kunden';

$_['iframe_title'] = 'iFrame';
$_['iframe_desc'] = 'Soll die Bezahlseite in einem Iframe angezeigt werden?';

$_['consumerInformation_title'] = 'Verrechnungsdaten des Konsumenten mitsenden';
$_['consumerInformation_descript'] = 'Weiterleitung der Rechnungs- und Versanddaten des Kunden an den Finanzdienstleister.';

$_['no'] = 'Inaktiv';
$_['yes'] = 'Aktiv';

$_['basketData_title'] = 'Warenkorbdaten des Konsumenten mitsenden';
$_['basketData_descript'] = 'Weiterletung des Warenkorbs des Kunden an den Finanzdienstleister';

$_['payolution'] = 'payolution';
$_['qenta'] = 'QENTA';
$_['ratepay'] = 'RatePay';

