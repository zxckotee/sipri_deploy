<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="card-body">
            <h4 class="mb10"><?php echo app_lang("sipri"); ?></h4>
            <div class="text-off">
                <?php echo app_lang("sipri_mode_help"); ?>
            </div>
            <div class="mt15">
                <?php echo anchor(get_uri("sipri/ideas"), app_lang("sipri_ideas"), array("class" => "btn btn-primary")); ?>
                <?php echo anchor(get_uri("sipri/approvals"), app_lang("sipri_approvals"), array("class" => "btn btn-default")); ?>
                <?php echo anchor(get_uri("sipri/contacts"), app_lang("sipri_contacts"), array("class" => "btn btn-default")); ?>
                <?php echo anchor(get_uri("sipri/documents"), app_lang("sipri_documents"), array("class" => "btn btn-default")); ?>
                <?php echo anchor(get_uri("sipri/profile"), app_lang("my_profile"), array("class" => "btn btn-default")); ?>
                <?php echo anchor(get_uri("sipri/settings"), app_lang("settings"), array("class" => "btn btn-default")); ?>
                <?php echo anchor(get_uri("sipri/support"), app_lang("help"), array("class" => "btn btn-default")); ?>
                <?php if ($login_user->is_admin || get_array_value($login_user->permissions, "sipri_manage")) { ?>
                    <?php echo anchor(get_uri("sipri/admin"), app_lang("sipri_admin_panel"), array("class" => "btn btn-default")); ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>


