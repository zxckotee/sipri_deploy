<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h4><?php echo $idea->title; ?></h4>
            <div class="title-button-group">
                <?php
                echo modal_anchor(get_uri("sipri/ideas/modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang("edit"), array("class" => "btn btn-default", "title" => app_lang("edit"), "data-post-id" => $idea->id));
                ?>
            </div>
        </div>
        <div class="card-body">
            <div class="mb10"><strong><?php echo app_lang("status"); ?>:</strong> <?php echo $idea->status; ?>, <strong>Rating:</strong> <?php echo (int) $idea->rating_percent; ?>%</div>
            <div class="text-off mb15">
                <div><strong><?php echo app_lang("created_by"); ?>:</strong> <?php echo $idea->created_by_name; ?></div>
                <div><strong><?php echo app_lang("created_date"); ?>:</strong> <?php echo format_to_date($idea->created_at); ?></div>
                <?php if ($idea->executor_user_id) { ?>
                    <div><strong><?php echo app_lang("assignee"); ?>:</strong> <?php echo $idea->executor_name ? $idea->executor_name : $idea->executor_user_id; ?></div>
                <?php } ?>
            </div>

            <?php if ($idea->problem) { ?>
                <h5>Проблема</h5>
                <div class="mb15"><?php echo nl2br($idea->problem); ?></div>
            <?php } ?>
            <?php if ($idea->idea) { ?>
                <h5>Предложение / идея</h5>
                <div class="mb15"><?php echo nl2br($idea->idea); ?></div>
            <?php } ?>
            <?php if ($idea->how_to) { ?>
                <h5>Как выполнить</h5>
                <div class="mb15"><?php echo nl2br($idea->how_to); ?></div>
            <?php } ?>
            <?php if ($idea->resources) { ?>
                <h5>Требуемые ресурсы</h5>
                <div class="mb15"><?php echo nl2br($idea->resources); ?></div>
            <?php } ?>

            <hr />
            <h5>Голосование</h5>
            <div class="mb10">
                <span class="badge bg-success">За: <?php echo (int) $vote_counts->yes_count; ?></span>
                <span class="badge bg-danger ms-1">Против: <?php echo (int) $vote_counts->no_count; ?></span>
                <span class="badge bg-secondary ms-1">Воздерж.: <?php echo (int) $vote_counts->abstain_count; ?></span>
            </div>
            <div class="mb15">
                <button class="btn btn-success" type="button" onclick="sipriVote('yes')">За</button>
                <button class="btn btn-danger" type="button" onclick="sipriVote('no')">Против</button>
                <button class="btn btn-default" type="button" onclick="sipriVote('abstain')">Воздерж.</button>
            </div>

            <?php if ($attachments && count($attachments)) { ?>
                <hr />
                <h5><?php echo app_lang("files"); ?></h5>
                <?php foreach ($attachments as $att) { ?>
                    <?php
                    $files = $att->files ? @unserialize($att->files) : [];
                    ?>
                    <?php if (is_array($files) && count($files)) { ?>
                        <ul class="list-unstyled">
                            <?php foreach ($files as $f) { ?>
                                <li>
                                    <?php
                                    $name = get_array_value($f, "file_name");
                                    $url = get_file_uri(get_setting("timeline_file_path") . $name);
                                    echo anchor($url, remove_file_prefix($name), array("target" => "_blank"));
                                    ?>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                <?php } ?>
            <?php } ?>

            <hr />
            <h5>Обсуждение</h5>
            <div class="mb15">
                <?php echo form_open(get_uri("sipri/ideas/add_comment"), array("id" => "sipri-comment-form", "class" => "general-form", "role" => "form")); ?>
                <input type="hidden" name="idea_id" value="<?php echo $idea->id; ?>" />
                <div class="form-group">
                    <textarea name="comment" class="form-control" rows="3" placeholder="Комментарий" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>"></textarea>
                </div>
                <div class="form-group">
                    <?php echo form_checkbox("is_anonymous", "1", false, "id='comment_is_anonymous' class='form-check-input'"); ?>
                    <label for="comment_is_anonymous">Анонимно</label>
                </div>
                <button class="btn btn-primary" type="submit"><?php echo app_lang("save"); ?></button>
                <?php echo form_close(); ?>
            </div>

            <div>
                <?php foreach ($comments as $c) { ?>
                    <div class="mb10">
                        <div>
                            <strong><?php echo $c->is_anonymous ? "Анонимно" : ($c->user_name ? $c->user_name : app_lang("user")); ?></strong>
                            <span class="text-off ms-2"><?php echo format_to_relative_time($c->created_at); ?></span>
                        </div>
                        <div><?php echo nl2br($c->comment); ?></div>
                    </div>
                <?php } ?>
                <?php if (!count($comments)) { ?>
                    <div class="text-off"><?php echo app_lang("no_data_found"); ?></div>
                <?php } ?>
            </div>

            <hr />
            <h5><?php echo app_lang("sipri_approvals"); ?></h5>
            <?php if ($can_approve) { ?>
                <div class="mb10">
                    <?php echo form_open(get_uri("sipri/ideas/send_for_approval"), array("id" => "sipri-send-approval-form", "class" => "general-form", "role" => "form")); ?>
                    <input type="hidden" name="idea_id" value="<?php echo $idea->id; ?>" />
                    <div class="row">
                        <div class="col-md-8">
                            <select name="approver_user_id" class="form-control">
                                <option value=""><?php echo app_lang("sipri_auto_by_org"); ?></option>
                                <?php
                                // reuse existing users list (staff only)
                                $Users_model = model("App\\Models\\Users_model");
                                $users = $Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->getResult();
                                foreach ($users as $u) {
                                    echo "<option value='$u->id'>" . $u->first_name . " " . $u->last_name . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-default" type="submit"><?php echo app_lang("sipri_send_for_approval"); ?></button>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>
            <?php } ?>

            <?php if ($approvals && count($approvals)) { ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Согласующий</th>
                                <th>Решение</th>
                                <th>Комментарий</th>
                                <th>Дата</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($approvals as $a) { ?>
                                <tr>
                                    <td><?php echo $a->approver_name; ?></td>
                                    <td><?php echo $a->decision ? $a->decision : "—"; ?></td>
                                    <td><?php echo $a->comment ? $a->comment : ""; ?></td>
                                    <td><?php echo $a->decided_at ? format_to_date($a->decided_at) : ""; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="text-off">Нет назначенных согласований.</div>
            <?php } ?>

            <hr />
            <h5><?php echo app_lang("sipri_execution"); ?></h5>
            <?php
            $is_manager = ($login_user->is_admin || get_array_value($login_user->permissions, "sipri_manage") || get_array_value($login_user->permissions, "sipri_approve"));
            $is_executor = ($idea->executor_user_id && (int) $idea->executor_user_id === (int) $login_user->id);
            ?>

            <?php if ($is_manager) { ?>
                <?php echo form_open(get_uri("sipri/ideas/assign_executor"), array("id" => "sipri-assign-executor-form", "class" => "general-form", "role" => "form")); ?>
                <input type="hidden" name="idea_id" value="<?php echo $idea->id; ?>" />
                <div class="row mb10">
                    <div class="col-md-8">
                        <select name="executor_user_id" class="form-control" data-rule-required="true" data-msg-required="<?php echo app_lang('field_required'); ?>">
                            <option value=""><?php echo app_lang("select"); ?></option>
                            <?php foreach ($users as $u) { ?>
                                <option value="<?php echo $u->id; ?>" <?php echo ($idea->executor_user_id == $u->id) ? "selected" : ""; ?>><?php echo $u->first_name . " " . $u->last_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-default" type="submit"><?php echo app_lang("sipri_assign_executor"); ?></button>
                    </div>
                </div>
                <?php echo form_close(); ?>
            <?php } ?>

            <?php if ($is_executor && $idea->status === "in_progress") { ?>
                <?php echo form_open(get_uri("sipri/ideas/mark_done"), array("id" => "sipri-mark-done-form", "class" => "general-form", "role" => "form")); ?>
                <input type="hidden" name="idea_id" value="<?php echo $idea->id; ?>" />
                <div class="form-group">
                    <label><?php echo app_lang("sipri_execution_report"); ?></label>
                    <textarea name="execution_report" class="form-control" rows="3" placeholder="<?php echo app_lang("sipri_execution_report"); ?>"></textarea>
                </div>
                <div class="mb10">
                    <button class="btn btn-success" type="submit" name="result" value="done_success"><?php echo app_lang("sipri_done_success"); ?></button>
                    <button class="btn btn-danger" type="submit" name="result" value="done_fail"><?php echo app_lang("sipri_done_fail"); ?></button>
                </div>
                <?php echo form_close(); ?>
            <?php } ?>

            <?php if (in_array($idea->status, array("done_success", "done_fail")) && ($login_user->is_admin || get_array_value($login_user->permissions, "sipri_manage"))) { ?>
                <?php echo form_open(get_uri("sipri/ideas/archive"), array("id" => "sipri-archive-form", "class" => "general-form", "role" => "form")); ?>
                <input type="hidden" name="idea_id" value="<?php echo $idea->id; ?>" />
                <button class="btn btn-default" type="submit"><?php echo app_lang("sipri_send_to_archive"); ?></button>
                <?php echo form_close(); ?>
            <?php } ?>

            <?php if ($idea->execution_report) { ?>
                <div class="mt10">
                    <div><strong><?php echo app_lang("sipri_execution_report"); ?>:</strong></div>
                    <div class="text-off"><?php echo nl2br($idea->execution_report); ?></div>
                </div>
            <?php } ?>

            <?php if ($history && count($history)) { ?>
                <hr />
                <h5>История статусов</h5>
                <ul class="list-unstyled">
                    <?php foreach ($history as $h) { ?>
                        <li class="mb5">
                            <span class="text-off"><?php echo format_to_date($h->created_at); ?></span>
                            — <strong><?php echo $h->from_status ? $h->from_status : "—"; ?></strong>
                            → <strong><?php echo $h->to_status; ?></strong>
                            <?php if ($h->changed_by_name) { ?>
                                (<?php echo $h->changed_by_name; ?>)
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    feather.replace();

    function sipriVote(vote) {
        $.ajax({
            url: "<?php echo get_uri('sipri/ideas/vote'); ?>",
            type: "POST",
            dataType: "json",
            data: {idea_id: "<?php echo $idea->id; ?>", vote: vote},
            success: function (res) {
                if (res && res.success) {
                    window.location.reload();
                } else {
                    appAlert.error(res.message || "<?php echo app_lang('error_occurred'); ?>", {duration: 4000});
                }
            }
        });
    }

    $(document).ready(function () {
        $("#sipri-comment-form").appForm({
            onSuccess: function () {
                window.location.reload();
            }
        });

        $("#sipri-send-approval-form").appForm({
            onSuccess: function () {
                window.location.reload();
            }
        });

        $("#sipri-assign-executor-form").appForm({
            onSuccess: function () {
                window.location.reload();
            }
        });

        $("#sipri-mark-done-form").appForm({
            onSuccess: function () {
                window.location.reload();
            }
        });

        $("#sipri-archive-form").appForm({
            onSuccess: function () {
                window.location.reload();
            }
        });
    });
</script>


