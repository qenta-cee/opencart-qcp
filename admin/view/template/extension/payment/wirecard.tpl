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

<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-wirecard" data-toggle="tooltip"
						title="<?php echo $arrayLanguageText['button_save']; ?>"
						class="btn btn-primary">
					<i class="fa fa-save"></i>
				</button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip"
				   title="<?php echo $arrayLanguageText['button_cancel']; ?>"
				   class="btn btn-default">
					<i class="fa fa-reply"></i>
				</a>
			</div>
			<h1>
				<?php echo $arrayLanguageText['heading_title'];?>
			</h1>
			<ul class="breadcrumb">
				<?php foreach ($breadcrumbs as $breadcrumb): ?>
				<li>
					<a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
	<div class="container-fluid">
		<?php if ($boolHasValidationError === true): ?>
		<div class="alert alert-danger">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php foreach($arrayInputFieldsMandatory as $fieldname => $isError): ?>
			<?php if ($isError === true) echo '<i class="fa fa-exclamation-circle"></i><span class="error">&nbsp;' . $arrayErrorText[$fieldname] . '</span><br>';
			?>
			<?php endforeach; ?>
		</div>
		<?php endif; ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-pencil"></i><?php echo $arrayLanguageText['text_edit']; ?>
					&nbsp;<?php echo $arrayLanguageText['heading_title']; ?>
				</h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data"
					  id="form-wirecard"
					  class="form-horizontal">
					<!-- Enable -->
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-enabled">
                            <span title=""
								  data-toggle="tooltip"
								  data-original-title="<?php echo $arrayLanguageText['enable_descript']; ?>">
                                        <?php echo $arrayLanguageText['enable_title']; ?>
                            </span>
						</label>
						<div class="col-sm-10">
							<select class="form-control" id="input-enabled" name="<?php echo $status; ?>">
								<?php if ($input[$prefix.'status']) { ?>
								<option value="1"
										selected="selected"><?php echo $arrayLanguageText['text_enabled']; ?></option>
								<option value="0"><?php echo $arrayLanguageText['text_disabled']; ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $arrayLanguageText['text_enabled']; ?></option>
								<option value="0"
										selected="selected"><?php echo $arrayLanguageText['text_disabled']; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<!-- END Enable -->
					<!-- Fields -->
					<?php foreach($inputfields as $fieldname => $fieldtype): ?>
					<?php if($fieldtype == 'input'): ?>
					<div class="form-group <?php if (array_key_exists($fieldname, $arrayInputFieldsMandatory)) echo "
						 required
					";?>">
					<label class="col-sm-2 control-label"
						   for="input-<?php echo $prefix.$fieldname; ?>">
                            <span title=""
								  data-toggle="tooltip"
								  data-original-title="<?php echo $arrayLanguageText[$fieldname . '_descript']; ?>">
                                        <?php echo $arrayLanguageText[$fieldname . '_title']; ?>
                            </span>
					</label>
					<div class="col-sm-10">
						<input class="form-control"
							   id="input-<?php echo $prefix.$fieldname; ?>"
							   type="text" name="<?php echo $prefix.$fieldname;?>"
							   value="<?php echo $input[$prefix.$fieldname];?>">
					</div>
			</div>
			<?php endif; ?>
			<?php if($fieldtype == 'textarea'): ?>
			<div class="form-group <?php if (array_key_exists($fieldname, $arrayInputFieldsMandatory)) echo " required
			";?>">
			<label class="col-sm-2 control-label"
				   for="textarea-<?php echo $prefix.$fieldname;?>">
                            <span title=""
								  data-toggle="tooltip"
								  data-original-title="<?php echo $arrayLanguageText[$fieldname . '_descript']; ?>">
                                        <?php echo $arrayLanguageText[$fieldname . '_title']; ?>
                            </span>
			</label>
			<div class="col-sm-10">
                            <textarea class="form-control"
									  id="textarea-<?php echo $prefix.$fieldname;?>"
									  name="<?php echo $prefix.$fieldname;?>"><?php echo $input[$prefix.$fieldname];?></textarea>
			</div>
		</div>
		<?php endif; ?>
		<?php if($fieldtype == 'true_false'): ?>
		<div class="form-group <?php if (array_key_exists($fieldname, $arrayInputFieldsMandatory)) echo " required
		";?>">
		<label class="col-sm-2 control-label"
			   for="select-<?php echo $prefix.$fieldname;?>">
                           <span title=""
								 data-toggle="tooltip"
								 data-original-title="<?php echo $arrayLanguageText[$fieldname . '_descript']; ?>">
                                        <?php echo $arrayLanguageText[$fieldname . '_title']; ?>
                            </span>
		</label>
		<div class="col-sm-10">
			<select class="form-control"
					id="select-<?php echo $prefix.$fieldname;?>"
					name="<?php echo $prefix.$fieldname;?>">
				<option value="0"><?php echo $arrayLanguageText['no']; ?></option>
				<?php if ($input[$prefix.$fieldname] == '1' ): ?>
				<option value="1" selected="selected"><?php echo $arrayLanguageText['yes']; ?>
				</option>
				<?php else:?>
				<option value="1"><?php echo $arrayLanguageText['yes']; ?></option>
				<?php endif; ?>
			</select>
		</div>
	</div>
	<?php endif; ?>

	<?php if($fieldtype == 'select_provider'): ?>
	<div class="form-group <?php if (array_key_exists($fieldname, $arrayInputFieldsMandatory)) echo " required
	";?>">
	<label class="col-sm-2 control-label"
		   for="select-<?php echo $prefix.$fieldname;?>">
                           <span title=""
								 data-toggle="tooltip"
								 data-original-title="<?php echo $arrayLanguageText[$fieldname . '_descript']; ?>">
                                        <?php echo $arrayLanguageText[$fieldname . '_title']; ?>
                            </span>
	</label>
	<div class="col-sm-10">
		<select class="form-control"
				id="select-<?php echo $prefix.$fieldname;?>"
				name="<?php echo $prefix.$fieldname;?>">
			<?php if ($input[$prefix.$fieldname] == 'payolution') :?><option value="payolution" selected="selected"><?php else: ?><option value="payolution"><?php endif; ?><?php echo $arrayLanguageText['payolution']; ?></option>
			<?php if ($prefix.$fieldname == 'wirecard_invoice_provider' ): ?>
				<?php if ($input[$prefix.$fieldname] == 'wirecard') :?>
					<option value="wirecard" selected="selected">
				<?php else: ?>
					<option value="wirecard">
				<?php endif; ?>
			<?php echo $arrayLanguageText['wirecard']; ?>
			</option>
			<?php endif; ?>
			<?php if ($input[$prefix.$fieldname] == 'ratepay') :?><option value="ratepay" selected="selected">
				<?php else: ?><option value="ratepay">
				<?php endif; ?><?php echo $arrayLanguageText['ratepay']; ?></option>
		</select>
	</div>
</div>
<?php endif; ?>

<?php if($fieldtype == 'select_country'): ?>
<div class="form-group <?php if (array_key_exists($fieldname, $arrayInputFieldsMandatory)) echo " required ";?>">
<label class="col-sm-2 control-label"
	   for="select-<?php echo $prefix.$fieldname;?>">
                           <span title=""
								 data-toggle="tooltip"
								 data-original-title="<?php echo $arrayLanguageText[$fieldname . '_descript']; ?>">
                                        <?php echo $arrayLanguageText[$fieldname . '_title']; ?>
                            </span>
</label>
<div class="col-sm-10">
	<select multiple class="form-control"
			id="select-<?php echo $prefix.$fieldname;?>"
			name="<?php echo $prefix.$fieldname;?>[]">
		<?php foreach ($countries as $country) { ?>
		<?php if (isset($input[$prefix.$fieldname]) && in_array($country['country_id'], $input[$prefix.$fieldname])) { ?>
		<option value="<?php echo $country['country_id']; ?>"
				selected="selected"><?php echo $country['name']; ?></option>
		<?php } else { ?>
		<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
		<?php } ?>
		<?php } ?>
	</select>
</div>
</div>
<?php endif; ?>

<?php if($fieldtype == 'select_currency'): ?>
<div class="form-group <?php if (array_key_exists($fieldname, $arrayInputFieldsMandatory)) echo " required ";?>">
<label class="col-sm-2 control-label"
	   for="select-<?php echo $prefix.$fieldname;?>">
                           <span title=""
								 data-toggle="tooltip"
								 data-original-title="<?php echo $arrayLanguageText[$fieldname . '_descript']; ?>">
                                        <?php echo $arrayLanguageText[$fieldname . '_title']; ?>
                            </span>
</label>
<div class="col-sm-10">
	<select multiple class="form-control"
			id="select-<?php echo $prefix.$fieldname;?>"
			name="<?php echo $prefix.$fieldname;?>[]">
		<?php foreach ($currencies as $currency) { ?>
		<?php if (isset($input[$prefix.$fieldname]) && in_array($currency['code'], $input[$prefix.$fieldname])) { ?>
		<option value="<?php echo $currency['code']; ?>"
				selected="selected"><?php echo $currency['code']; ?></option>
		<?php } else { ?>
		<option value="<?php echo $currency['code']; ?>"><?php echo $currency['code']; ?></option>
		<?php } ?>
		<?php } ?>
	</select>
</div>
</div>
<?php endif; ?>

	<?php if($fieldtype == 'status_code'): ?>
	<div class="form-group <?php if (array_key_exists($fieldname, $arrayInputFieldsMandatory)) echo " required
	";?>">
	<label class="col-sm-2 control-label"
		   for="select-<?php echo $prefix.$fieldname;?>">
                           <span title=""
								 data-toggle="tooltip"
								 data-original-title="<?php echo $arrayLanguageText[$fieldname . '_descript']; ?>">
                                        <?php echo $arrayLanguageText[$fieldname . '_title']; ?>
                            </span>
	</label>
	<div class="col-sm-10">
		<select class="form-control"
				id="select-<?php echo $prefix.$fieldname;?>"
				name="<?php echo $prefix.$fieldname;?>">
			<?php foreach ($order_statuses as $order_status):?>
			<?php if ($order_status['order_status_id'] == $input[$prefix.$fieldname]) : ?>
			<option value="<?php echo $order_status['order_status_id']; ?>"
					selected="selected"><?php echo $order_status['name']; ?></option>
			<?php elseif($order_status['order_status_id'] == '7' &&  $prefix.$fieldname == ''): ?>
			<option value="<?php echo $order_status['order_status_id']; ?>"
					selected="selected"><?php echo $order_status['name']; ?></option>
			<?php else: ?>
			<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
			<?php endif; ?>
			<?php endforeach; ?>
		</select>
	</div>
</div>
<?php endif; ?>
<?php endforeach; ?>
<!-- END Fields -->
</form>
<!-- License Text -->
<div class="col-sm-12 divider">
	<hr>
	<?php echo $arrayLanguageText['license_text']; ?>
</div>
<!-- END License Text -->
</div>
</div>
</div>
</div>
<?php echo $footer; ?>