<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="card-body">
            <h4 class="mb10"><?php echo app_lang("my_profile"); ?> (<?php echo app_lang("sipri"); ?>)</h4>
            <?php if (!$login_user->sipri_profile_completed) { ?>
                <div class="alert alert-warning mb15"><?php echo app_lang("sipri_profile_required"); ?></div>
            <?php } ?>

            <?php echo form_open(get_uri("sipri/profile/save"), array("id" => "sipri-profile-form", "class" => "general-form", "role" => "form")); ?>

            <div class="form-group">
                <?php echo form_checkbox("sipri_is_anonymous", "1", $login_user->sipri_is_anonymous ? true : false, "id='sipri_is_anonymous' class='form-check-input'"); ?>
                <label for="sipri_is_anonymous"><?php echo app_lang("sipri_anonymous_mode"); ?></label>
                <div class="text-off mt5"><?php echo app_lang("sipri_anonymous_mode_help"); ?></div>
            </div>

            <div id="sipri_nickname_area" class="form-group <?php echo $login_user->sipri_is_anonymous ? "" : "hide"; ?>">
                <label for="sipri_nickname"><?php echo app_lang("sipri_nickname"); ?></label>
                <?php echo form_input(array("id" => "sipri_nickname", "name" => "sipri_nickname", "value" => $login_user->sipri_nickname, "class" => "form-control")); ?>
            </div>

            <div id="sipri_org_area" class="<?php echo $login_user->sipri_is_anonymous ? "hide" : ""; ?>">
                <div class="form-group">
                    <label for="sipri_position"><?php echo app_lang("sipri_position"); ?></label>
                    <?php echo form_input(array("id" => "sipri_position", "name" => "sipri_position", "value" => $login_user->sipri_position, "class" => "form-control")); ?>
                </div>

                <div class="form-group">
                    <label for="sipri_work_email"><?php echo app_lang("sipri_work_email"); ?></label>
                    <?php echo form_input(array("id" => "sipri_work_email", "name" => "sipri_work_email", "value" => $login_user->sipri_work_email, "class" => "form-control")); ?>
                </div>

                <div class="form-group">
                    <label for="sipri_personal_email"><?php echo app_lang("sipri_personal_email"); ?></label>
                    <?php echo form_input(array("id" => "sipri_personal_email", "name" => "sipri_personal_email", "value" => $login_user->sipri_personal_email, "class" => "form-control")); ?>
                </div>

                <div class="form-group">
                    <label for="sipri_tenant_id"><?php echo app_lang("sipri_tenant"); ?></label>
                    <select id="sipri_tenant_id" name="sipri_tenant_id" class="form-control">
                        <option value=""><?php echo app_lang("select"); ?></option>
                        <?php foreach ($tenants as $t) { ?>
                            <option value="<?php echo $t->id; ?>" <?php echo ($login_user->sipri_tenant_id == $t->id) ? "selected" : ""; ?>>
                                <?php echo $t->name; ?>
                            </option>
                        <?php } ?>
                    </select>
                    <?php if (!count($tenants)) { ?>
                        <div class="text-off mt5"><?php echo app_lang("sipri_no_tenants_hint"); ?></div>
                    <?php } ?>
                </div>

                <div class="form-group">
                    <label for="sipri_department_id"><?php echo app_lang("sipri_department"); ?></label>
                    <select id="sipri_department_id" name="sipri_department_id" class="form-control">
                        <option value=""><?php echo app_lang("select"); ?></option>
                        <?php foreach ($departments as $d) { ?>
                            <option value="<?php echo $d->id; ?>" <?php echo ($login_user->sipri_department_id == $d->id) ? "selected" : ""; ?>>
                                <?php echo $d->name; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <button class="btn btn-primary" type="submit"><?php echo app_lang("save"); ?></button>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#sipri-profile-form").appForm({
            onSuccess: function () {
                window.location.reload();
            }
        });

        $("#sipri_is_anonymous").on("change", function () {
            var on = $(this).is(":checked");
            if (on) {
                $("#sipri_org_area").addClass("hide");
                $("#sipri_nickname_area").removeClass("hide");
            } else {
                $("#sipri_org_area").removeClass("hide");
                $("#sipri_nickname_area").addClass("hide");
            }
        });

        $("#sipri_tenant_id").on("change", function () {
            var tenantId = $(this).val();
            $("#sipri_department_id").html("<option value=''><?php echo app_lang('select'); ?></option>");
            if (!tenantId) {
                return;
            }
            $.ajax({
                url: "<?php echo get_uri('sipri/profile/departments_json'); ?>",
                dataType: "json",
                data: {tenant_id: tenantId},
                success: function (items) {
                    var html = "<option value=''><?php echo app_lang('select'); ?></option>";
                    if (items && items.length) {
                        for (var i = 0; i < items.length; i++) {
                            html += "<option value='" + items[i].id + "'>" + items[i].text + "</option>";
                        }
                    }
                    $("#sipri_department_id").html(html);
                }
            });
        });
    });
</script>


