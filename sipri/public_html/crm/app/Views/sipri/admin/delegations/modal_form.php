<?php echo form_open(get_uri("sipri/delegations/save"), array("id" => "sipri-delegation-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

        <div class="form-group">
            <div class="row">
                <label for="tenant_id" class="col-md-3"><?php echo app_lang("sipri_tenant"); ?></label>
                <div class="col-md-9">
                    <?php echo form_dropdown("tenant_id", $tenant_dropdown, $model_info->tenant_id, "class='select2 validate-hidden' id='tenant_id' data-rule-required='true' data-msg-required='" . app_lang("field_required") . "'"); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="from_user_id" class="col-md-3"><?php echo app_lang("from"); ?></label>
                <div class="col-md-9">
                    <input type="text" value="<?php echo $model_info->from_user_id; ?>" name="from_user_id" id="from_user_id" class="w100p validate-hidden" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="to_user_id" class="col-md-3"><?php echo app_lang("to"); ?></label>
                <div class="col-md-9">
                    <input type="text" value="<?php echo $model_info->to_user_id; ?>" name="to_user_id" id="to_user_id" class="w100p validate-hidden" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="starts_at" class="col-md-3"><?php echo app_lang("start_date"); ?></label>
                <div class="col-md-9">
                    <?php echo form_input(array("id" => "starts_at", "name" => "starts_at", "value" => $model_info->starts_at, "class" => "form-control", "placeholder" => "YYYY-MM-DD HH:MM:SS")); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="ends_at" class="col-md-3"><?php echo app_lang("end_date"); ?></label>
                <div class="col-md-9">
                    <?php echo form_input(array("id" => "ends_at", "name" => "ends_at", "value" => $model_info->ends_at, "class" => "form-control", "placeholder" => "YYYY-MM-DD HH:MM:SS")); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="is_active" class="col-md-3"><?php echo app_lang("status"); ?></label>
                <div class="col-md-9 pt5">
                    <?php echo form_checkbox("is_active", "1", $model_info->id ? ($model_info->is_active ? true : false) : true, "id='is_active' class='form-check-input'"); ?>
                    <label for="is_active"><?php echo app_lang("active"); ?></label>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><span data-feather="x" class="icon-16"></span> <?php echo app_lang("close"); ?></button>
    <button type="submit" class="btn btn-primary"><span data-feather="check-circle" class="icon-16"></span> <?php echo app_lang("save"); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#sipri-delegation-form").appForm({
            onSuccess: function (result) {
                $("#sipri-delegations-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        $("#from_user_id").select2({multiple: false, data: <?php echo ($members_dropdown); ?>});
        $("#to_user_id").select2({multiple: false, data: <?php echo ($members_dropdown); ?>});
        $("#sipri-delegation-form .select2").select2();
    });
</script>


