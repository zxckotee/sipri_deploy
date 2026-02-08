<?php echo form_open(get_uri("sipri/ideas/save"), array("id" => "sipri-idea-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />

    <div class="form-group">
        <label for="title"><?php echo app_lang("title"); ?></label>
        <?php echo form_input(array("id" => "title", "name" => "title", "value" => $model_info->title, "class" => "form-control", "data-rule-required" => true, "data-msg-required" => app_lang("field_required"))); ?>
    </div>

    <div class="form-group">
        <label for="category"><?php echo app_lang("type"); ?></label>
        <?php
        echo form_dropdown("category", array(
            "enterprise" => "enterprise",
            "client" => "client",
            "partner" => "partner"
        ), $model_info->category ? $model_info->category : "enterprise", "id='category' class='form-control' data-rule-required='true' data-msg-required='" . app_lang("field_required") . "'");
        ?>
    </div>

    <div class="form-group">
        <label for="problem"><?php echo "Проблема"; ?></label>
        <?php echo form_textarea(array("id" => "problem", "name" => "problem", "value" => $model_info->problem, "class" => "form-control", "rows" => 3)); ?>
    </div>

    <div class="form-group">
        <label for="idea"><?php echo "Предложение / идея"; ?></label>
        <?php echo form_textarea(array("id" => "idea", "name" => "idea", "value" => $model_info->idea, "class" => "form-control", "rows" => 3)); ?>
    </div>

    <div class="form-group">
        <label for="how_to"><?php echo "Как выполнить"; ?></label>
        <?php echo form_textarea(array("id" => "how_to", "name" => "how_to", "value" => $model_info->how_to, "class" => "form-control", "rows" => 3)); ?>
    </div>

    <div class="form-group">
        <label for="resources"><?php echo "Требуемые ресурсы"; ?></label>
        <?php echo form_textarea(array("id" => "resources", "name" => "resources", "value" => $model_info->resources, "class" => "form-control", "rows" => 3)); ?>
    </div>

    <div class="form-group">
        <div>
            <?php echo form_checkbox("is_anonymous", "1", $model_info->is_anonymous ? true : false, "id='is_anonymous' class='form-check-input'"); ?>
            <label for="is_anonymous">Анонимно</label>
        </div>
        <div class="mt5">
            <?php echo form_checkbox("is_secret", "1", $model_info->is_secret ? true : false, "id='is_secret' class='form-check-input'"); ?>
            <label for="is_secret"><?php echo app_lang("private"); ?></label>
        </div>
    </div>

    <?php echo view("includes/multi_file_uploader"); ?>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php echo app_lang('close'); ?></button>
    <button type="submit" class="btn btn-primary"><?php echo app_lang('save'); ?></button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        $("#sipri-idea-form").appForm({
            onSuccess: function (result) {
                appAlert.success(result.message, {duration: 3000});
                window.location.reload();
            }
        });
    });
</script>


