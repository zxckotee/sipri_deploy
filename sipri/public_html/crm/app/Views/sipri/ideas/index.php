<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="page-title clearfix">
            <h4><?php echo app_lang("sipri_ideas"); ?></h4>
            <div class="title-button-group">
                <?php
                echo modal_anchor(get_uri("sipri/ideas/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang("add"), array("class" => "btn btn-default", "title" => app_lang("add")));
                ?>
            </div>
        </div>
        <div class="card-body">
            <?php echo form_open(get_uri("sipri/ideas"), array("method" => "get", "class" => "mb15")); ?>
            <div class="input-group">
                <input type="text" name="search" value="<?php echo $search; ?>" class="form-control" placeholder="<?php echo app_lang("search"); ?>" />
                <button class="btn btn-primary" type="submit"><?php echo app_lang("search"); ?></button>
            </div>
            <?php echo form_close(); ?>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th><?php echo app_lang("id"); ?></th>
                            <th><?php echo app_lang("title"); ?></th>
                            <th><?php echo app_lang("status"); ?></th>
                            <th><?php echo app_lang("created_by"); ?></th>
                            <th><?php echo app_lang("created_date"); ?></th>
                            <th><?php echo app_lang("options"); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ideas as $idea) { ?>
                            <tr>
                                <td><?php echo $idea->id; ?></td>
                                <td>
                                    <?php echo anchor(get_uri("sipri/ideas/view/" . $idea->id), $idea->title); ?>
                                    <?php if ($idea->is_secret) { ?>
                                        <span class="badge bg-danger ms-2"><?php echo app_lang("private"); ?></span>
                                    <?php } ?>
                                </td>
                                <td><?php echo $idea->status; ?> (<?php echo (int) $idea->rating_percent; ?>%)</td>
                                <td><?php echo $idea->created_by_name; ?></td>
                                <td><?php echo format_to_date($idea->created_at); ?></td>
                                <td>
                                    <?php
                                    echo modal_anchor(get_uri("sipri/ideas/modal_form"), "<i data-feather='edit' class='icon-16'></i>", array("class" => "edit", "title" => app_lang("edit"), "data-post-id" => $idea->id));
                                    ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php if (!count($ideas)) { ?>
                            <tr><td colspan="6" class="text-center text-off"><?php echo app_lang("no_data_found"); ?></td></tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    feather.replace();
</script>


