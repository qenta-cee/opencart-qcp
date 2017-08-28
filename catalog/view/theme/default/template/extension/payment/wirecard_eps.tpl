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
?>
<?php echo $wcp_ratepay; ?>
<form class="form-horizontal" action="<?php echo $action ?>" method="post" name="wirecard_checkout_page_send" id="payment">
    <input type="hidden" name="wirecard_checkout_page_window_name" value="<?php echo $window_name; ?>">
    <fieldset id="payment">
        <legend><?php echo $text_title; ?></legend>
        <div class="form-group required">
            <label class="col-sm-2 control-label" for="wcp_financialinstitution"><?php echo $text_financialinstitution; ?></label>
            <div class="col-sm-3">
                <select name="wcp_financialinstitution" id="input-wcp-financialinstitution" class="form-control required" required="required">
                    <?php foreach ($select_financialinstitution as $key => $value) { ?>
                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </fieldset>
    <div class="pull-right">
        <input type="submit" class="btn btn-primary" id="button-confirm" value="<?php echo $button_confirm; ?>" />
    </div>
</form>
