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
    <fieldset id="payment">
        <legend><?php echo $text_title; ?></legend>
        <input type="hidden" name="birthday" id="wcp-birthday" value="" />
        <div class="form-group required">
            <label class="col-sm-2 control-label" for="wcp_birthdate_day"><?php echo $text_birthday; ?></label>
            <div class="col-sm-3">
                <select name="wcp_birthdate_day" id="input-wcp-birthdate-day" onchange="checkbirthday();" class="form-control">
                    <?php foreach ($days as $day) { ?>
                    <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-3">
                <select name="wcp_birthdate_month" id="input-wcp-birthdate-month" onchange="checkbirthday();" class="form-control">
                    <?php foreach ($months as $month) { ?>
                    <option value="<?php echo $month; ?>"><?php echo $month; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-3">
                <select name="wcp_birthdate_year" id="input-wcp-birthdate-year" onchange="checkbirthday();" class="form-control">
                    <?php foreach ($years as $year) { ?>
                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-2"></div><div class="col-sm-9"><?php echo $text_birthday_information; ?></div></div>
        <?php if($provider == 'payolution' && $terms){ ?>
        <div class="form-group required">
            <label class="col-sm-2 control-label" for="wcp_payolution_cond"><?php echo $text_payolution_title; ?></label>
            <div class="col-sm-9"><span><input type="checkbox" required="required" id="wcp_payolution_cond" onchange="checkbirthday()" /></span>
                <span><?php echo $text_payolution_consent1;
                if (strlen($mId)) {
                    echo '<a id="wcp-payolutionlink" href="https://payment.payolution.com/payolution-payment/infoport/dataprivacyconsent?mId='.$mId.'" target="_blank">' . $text_payolution_link .'</a>';
                }else {
                    echo $text_payolution_link;
                }
                echo $text_payolution_consent2; ?>
                </span>
            </div>
        </div>
        <?php } ?>
    </fieldset>
    <div class="pull-right">
        <input type="submit" class="btn btn-primary" id="button-confirm" value=" <?php echo $send_order; ?> " />
    </div>
</form>

<script type="text/javascript">
    $(document).ready(function() {
        checkbirthday();
    });

    function checkbirthday() {
        var m = $('#input-wcp-birthdate-month').val();
        var d = $('#input-wcp-birthdate-day').val();

        var dateStr = $('#input-wcp-birthdate-year').val() + '-' + m + '-' + d;
        var minAge = 18;

        var birthdate = new Date(dateStr);
        var year = birthdate.getFullYear();
        var today = new Date();
        var limit = new Date((today.getFullYear() - minAge), today.getMonth(), today.getDate());
        if (birthdate <= limit) {
            $('#wcp-birthday').val(dateStr);
            $('#button-confirm').attr('disabled', false);
        }
        else {
            $('#wcp-birthday').val("");
            $('#button-confirm').attr('disabled', true);
        }
    };

    //</script>
