<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="card-body">
            <div class="clearfix mb15">
                <h4 class="float-start"><?php echo app_lang("sipri_delegations"); ?></h4>
                <div class="float-end d-flex">
                    <div class="me-2" style="min-width:260px;">
                        <?php echo form_dropdown("tenant_id_filter", $tenant_dropdown, "", "class='select2' id='tenant_id_filter'"); ?>
                    </div>
                    <?php echo modal_anchor(get_uri("sipri/delegations/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang("add"), array("class" => "btn btn-default", "title" => app_lang("add"))); ?>
                </div>
            </div>

            <div class="table-responsive">
                <table id="sipri-delegations-table" class="display" cellspacing="0" width="100%"></table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var $table = $("#sipri-delegations-table");

        function src() {
            var tenantId = $("#tenant_id_filter").val() || "";
            return '<?php echo_uri("sipri/delegations/list_data") ?>' + (tenantId ? ("?tenant_id=" + tenantId) : "");
        }

        $("#tenant_id_filter").select2();

        $table.appTable({
            source: src(),
            columns: [
                {title: '<?php echo app_lang("sipri_tenant"); ?>'},
                {title: '<?php echo app_lang("from"); ?>'},
                {title: '<?php echo app_lang("to"); ?>'},
                {title: '<?php echo app_lang("start_date"); ?>'},
                {title: '<?php echo app_lang("end_date"); ?>'},
                {title: '<?php echo app_lang("status"); ?>'},
                {title: '<i data-feather=\"menu\" class=\"icon-16\"></i>', "class": "text-center option w100"}
            ],
            printColumns: [0, 1, 2, 3, 4, 5]
        });

        $("#tenant_id_filter").on("change", function () {
            $table.appTable({reload: true, source: src()});
        });
    });
</script>


