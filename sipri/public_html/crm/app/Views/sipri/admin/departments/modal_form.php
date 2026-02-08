<?php echo form_open(get_uri("sipri/departments/save"), array("id" => "sipri-department-form", "class" => "general-form", "role" => "form")); ?>
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
                <label for="parent_id" class="col-md-3"><?php echo app_lang("parent"); ?></label>
                <div class="col-md-9">
                    <?php echo form_dropdown("parent_id", $parent_dropdown, $model_info->parent_id, "class='select2' id='parent_id'"); ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="name" class="col-md-3"><?php echo app_lang("name"); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "name",
                        "name" => "name",
                        "value" => $model_info->name,
                        "class" => "form-control",
                        "placeholder" => app_lang("name"),
                        "data-rule-required" => true,
                        "data-msg-required" => app_lang("field_required")
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="code" class="col-md-3"><?php echo app_lang("code"); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "code",
                        "name" => "code",
                        "value" => $model_info->code,
                        "class" => "form-control",
                        "placeholder" => app_lang("code")
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="manager_user_id" class="col-md-3"><?php echo app_lang("sipri_department_manager"); ?></label>
                <div class="col-md-9">
                    <input type="text" value="<?php echo $model_info->manager_user_id; ?>" name="manager_user_id" id="manager_user_id" class="w100p" />
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="deputy_user_id" class="col-md-3"><?php echo app_lang("sipri_department_deputy"); ?></label>
                <div class="col-md-9">
                    <input type="text" value="<?php echo $model_info->deputy_user_id; ?>" name="deputy_user_id" id="deputy_user_id" class="w100p" />
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
        $("#sipri-department-form").appForm({
            onSuccess: function (result) {
                $("#sipri-departments-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        $("#manager_user_id").select2({multiple: false, data: <?php echo ($members_dropdown); ?>});
        $("#deputy_user_id").select2({multiple: false, data: <?php echo ($members_dropdown); ?>});
        $("#sipri-department-form .select2").select2();
    });
</script>


