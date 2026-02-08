<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="card-body">
            <div class="clearfix mb15">
                <h4 class="float-start"><?php echo app_lang("sipri_tenants"); ?></h4>
                <div class="float-end">
                    <?php echo modal_anchor(get_uri("sipri/tenants/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang("add"), array("class" => "btn btn-default", "title" => app_lang("add"))); ?>
                </div>
            </div>

            <div class="table-responsive">
                <table id="sipri-tenants-table" class="display" cellspacing="0" width="100%"></table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#sipri-tenants-table").appTable({
            source: '<?php echo_uri("sipri/tenants/list_data") ?>',
            columns: [
                {title: '<?php echo app_lang("name"); ?>'},
                {title: '<?php echo app_lang("slug"); ?>'},
                {title: '<?php echo app_lang("sipri_director"); ?>'},
                {title: '<?php echo app_lang("status"); ?>'},
                {title: '<i data-feather=\"menu\" class=\"icon-16\"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3]
        });
    });
</script>


