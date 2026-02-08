<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="card-body">
            <h4 class="mb10"><?php echo app_lang("sipri_settings"); ?></h4>
            <div class="text-off mb15"><?php echo app_lang("sipri_settings_help"); ?></div>

            <?php
            $language_dropdown = get_language_list();
            ?>

            <?php echo form_open(get_uri("sipri/settings/save"), array("id" => "sipri-settings-form", "class" => "general-form", "role" => "form")); ?>
            <div class="form-group">
                <?php echo form_checkbox("enable_web_notification", "1", $login_user->enable_web_notification ? true : false, "id='enable_web_notification' class='form-check-input'"); ?>
                <label for="enable_web_notification"><?php echo app_lang("enable_web_notification"); ?></label>
            </div>

            <?php if (count($language_dropdown)) { ?>
                <div class="form-group">
                    <label for="language"><?php echo app_lang("language"); ?></label>
                    <?php echo form_dropdown("language", $language_dropdown, $login_user->language ? $login_user->language : get_setting("language"), "class='select2' id='language'"); ?>
                </div>
            <?php } ?>

            <button class="btn btn-primary" type="submit"><?php echo app_lang("save"); ?></button>
            <?php echo form_close(); ?>

            <hr />
            <h5 class="mb10"><?php echo app_lang("sipri_integration"); ?></h5>
            <div class="text-off">
                <?php echo app_lang("sipri_integration_help"); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#sipri-settings-form").appForm({
            onSuccess: function () {
                window.location.reload();
            }
        });
        $("#sipri-settings-form .select2").select2();
    });
</script>


