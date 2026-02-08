<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="card-body">
            <h4 class="mb10"><?php echo app_lang("sipri_admin_panel"); ?></h4>

            <div class="mb15">
                <?php echo anchor(get_uri("sipri/tenants"), app_lang("sipri_tenants"), array("class" => "btn btn-default")); ?>
                <?php echo anchor(get_uri("sipri/departments"), app_lang("sipri_departments"), array("class" => "btn btn-default")); ?>
                <?php echo anchor(get_uri("sipri/delegations"), app_lang("sipri_delegations"), array("class" => "btn btn-default")); ?>
            </div>

            <div class="alert alert-info">
                <div class="mb5"><strong><?php echo app_lang("sipri_admin_decision_title"); ?></strong></div>
                <div><?php echo app_lang("sipri_admin_decision_keep_standalone"); ?></div>
            </div>

            <div class="mb15">
                <?php echo form_open(get_uri("sipri/admin/save_settings"), array("id" => "sipri-admin-settings-form", "class" => "general-form", "role" => "form")); ?>
                <div class="form-group">
                    <label for="sipri_admin_panel_url"><?php echo app_lang("sipri_admin_panel_url_label"); ?></label>
                    <div class="input-group">
                        <input id="sipri_admin_panel_url" name="sipri_admin_panel_url" type="text" class="form-control" value="<?php echo get_setting('sipri_admin_panel_url'); ?>" />
                        <?php if (get_setting('sipri_admin_panel_url')) { ?>
                            <a class="btn btn-default" href="<?php echo get_setting('sipri_admin_panel_url'); ?>" target="_blank"><?php echo app_lang("open"); ?></a>
                        <?php } ?>
                    </div>
                    <div class="text-off mt5"><?php echo app_lang("sipri_admin_panel_url_hint"); ?></div>
                </div>
                <div class="form-group">
                    <?php echo form_checkbox("sipri_allow_clients", "1", get_setting("sipri_allow_clients") == "1" ? true : false, "id='sipri_allow_clients' class='form-check-input'"); ?>
                    <label for="sipri_allow_clients"><?php echo app_lang("sipri_allow_clients"); ?></label>
                    <div class="text-off mt5"><?php echo app_lang("sipri_allow_clients_help"); ?></div>
                </div>
                <button class="btn btn-primary" type="submit"><?php echo app_lang("save"); ?></button>
                <?php echo form_close(); ?>
            </div>

            <h5 class="mb10"><?php echo app_lang("sipri_admin_functions_to_migrate"); ?></h5>
            <ul class="list-unstyled">
                <li>- <?php echo app_lang("sipri_admin_fn_notifications"); ?></li>
                <li>- <?php echo app_lang("sipri_admin_fn_translations"); ?></li>
                <li>- <?php echo app_lang("sipri_admin_fn_dynamic_menu"); ?></li>
                <li>- <?php echo app_lang("sipri_admin_fn_boarding"); ?></li>
                <li>- <?php echo app_lang("sipri_admin_fn_tabs"); ?></li>
            </ul>

            <div class="text-off mt10"><?php echo app_lang("sipri_admin_next_step_hint"); ?></div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#sipri-admin-settings-form").appForm({
            onSuccess: function () {
                window.location.reload();
            }
        });
    });
</script>


