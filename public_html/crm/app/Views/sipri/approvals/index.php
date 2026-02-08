<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="card-body">
            <h4 class="mb10"><?php echo app_lang("sipri_approvals"); ?></h4>
            <div class="text-off mb15">Здесь показываются карточки, ожидающие вашего решения.</div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo app_lang("id"); ?></th>
                            <th><?php echo app_lang("title"); ?></th>
                            <th><?php echo app_lang("status"); ?></th>
                            <th><?php echo app_lang("created_by"); ?></th>
                            <th><?php echo app_lang("options"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending as $p) { ?>
                            <tr>
                                <td><?php echo $p->idea_id; ?></td>
                                <td><?php echo anchor(get_uri("sipri/ideas/view/" . $p->idea_id), $p->title); ?></td>
                                <td><?php echo $p->status; ?> (<?php echo (int) $p->rating_percent; ?>%)</td>
                                <td><?php echo $p->author_name; ?></td>
                                <td>
                                    <button class="btn btn-success btn-sm" type="button" onclick="sipriDecide(<?php echo $p->idea_id; ?>,'approved')">Одобрить</button>
                                    <button class="btn btn-danger btn-sm" type="button" onclick="sipriDecide(<?php echo $p->idea_id; ?>,'rejected')">Отклонить</button>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!count($pending)) { ?>
                            <tr><td colspan="5" class="text-center text-off"><?php echo app_lang("no_data_found"); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function sipriDecide(ideaId, decision) {
        appPrompt({
            title: decision === 'approved' ? 'Одобрить' : 'Отклонить',
            description: 'Комментарий (необязательно)',
            inputType: 'textarea',
            callback: function (comment) {
                $.ajax({
                    url: "<?php echo get_uri('sipri/approvals/decide'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {idea_id: ideaId, decision: decision, comment: comment || ''},
                    success: function (res) {
                        if (res && res.success) {
                            window.location.reload();
                        } else {
                            appAlert.error(res.message || "<?php echo app_lang('error_occurred'); ?>", {duration: 4000});
                        }
                    }
                });
            }
        });
    }
</script>


