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
?>

<h3><?php echo $txt_info; ?></h3>
<form action="<?php echo $action ?>" method="post" name="wirecard_checkout_page_send" id="payment">
    <input type="hidden" name="wirecard_checkout_page_window_name" value="<?php echo $window_name; ?>">

    <div class="form-group required">
        <label class="col-sm-1 control-label" for="input-cc-cvv2"><?php echo $txt_birthday; ?></label>
        <div class="col-sm-2">
            <input type="text" name="birthday" value="" id="input-cc-cvv2" class="form-control" />
        </div>
    </div>

    <div class="pull-right">
        <input type="submit" class="btn btn-primary" value=" <?php echo $send_order; ?> ">
    </div>
</form>


