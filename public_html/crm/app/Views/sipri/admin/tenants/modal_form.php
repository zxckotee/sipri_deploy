<?php echo form_open(get_uri("sipri/tenants/save"), array("id" => "sipri-tenant-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

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
                        "data-msg-required" => app_lang("field_required"),
                        "autofocus" => true
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="slug" class="col-md-3"><?php echo app_lang("slug"); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "slug",
                        "name" => "slug",
                        "value" => $model_info->slug,
                        "class" => "form-control",
                        "placeholder" => app_lang("slug")
                    ));
                    ?>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="corp_key" class="col-md-3"><?php echo app_lang("sipri_corp_key"); ?></label>
                <div class="col-md-9">
                    <?php
                    echo form_input(array(
                        "id" => "corp_key",
                        "name" => "corp_key",
                        "value" => $model_info->corp_key,
                        "class" => "form-control",
                        "placeholder" => app_lang("sipri_corp_key")
                    ));
                    ?>
                    <div class="text-off mt5"><?php echo app_lang("sipri_corp_key_help"); ?></div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <label for="director_user_id" class="col-md-3"><?php echo app_lang("sipri_director"); ?></label>
                <div class="col-md-9">
                    <input type="text" value="<?php echo $model_info->director_user_id; ?>" name="director_user_id" id="director_user_id" class="w100p" />
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
        $("#sipri-tenant-form").appForm({
            onSuccess: function (result) {
                $("#sipri-tenants-table").appTable({newData: result.data, dataId: result.id});
            }
        });

        $("#director_user_id").select2({
            multiple: false,
            data: <?php echo ($members_dropdown); ?>
        });
    });
</script>


