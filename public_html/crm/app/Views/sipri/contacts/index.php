<div id="page-content" class="page-wrapper clearfix">
    <div class="card">
        <div class="card-body">
            <h4 class="mb10"><?php echo app_lang("sipri_contacts"); ?></h4>
            <ul class="nav nav-tabs mb15">
                <li class="nav-item">
                    <a class="nav-link <?php echo $tab === 'colleagues' ? 'active' : ''; ?>" href="<?php echo get_uri('sipri/contacts?tab=colleagues'); ?>">
                        <?php echo app_lang("team_members"); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $tab === 'clients' ? 'active' : ''; ?>" href="<?php echo get_uri('sipri/contacts?tab=clients'); ?>">
                        <?php echo app_lang("clients"); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $tab === 'partners' ? 'active' : ''; ?>" href="<?php echo get_uri('sipri/contacts?tab=partners'); ?>">
                        <?php echo app_lang("sipri_partner_enterprises"); ?>
                    </a>
                </li>
            </ul>

            <?php if ($tab === "clients") { ?>
                <div class="text-off mb10"><?php echo app_lang("sipri_clients_contacts_hint"); ?></div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><?php echo app_lang("name"); ?></th>
                                <th><?php echo app_lang("company_name"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($client_contacts as $u) { ?>
                                <tr>
                                    <td>
                                        <span class="avatar avatar-xs me-2"><img src="<?php echo get_avatar($u->image); ?>" /></span>
                                        <?php echo $u->first_name . " " . $u->last_name; ?>
                                    </td>
                                    <td><?php echo $u->company_name ? $u->company_name : "—"; ?></td>
                                </tr>
                            <?php } ?>
                            <?php if (!count($client_contacts)) { ?>
                                <tr><td colspan="2" class="text-center text-off"><?php echo app_lang("no_data_found"); ?></td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else if ($tab === "partners") { ?>
                <div class="text-off mb10"><?php echo app_lang("sipri_partner_enterprises_hint"); ?></div>
                <div class="text-off mb10"><?php echo app_lang("sipri_partner_departments_hint"); ?></div>
                <div class="accordion" id="sipri-partners-accordion">
                    <?php foreach ($partner_tenants as $i => $t) { ?>
                        <?php $collapseId = "sipri_partner_" . $t->id; ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="<?php echo $collapseId; ?>_h">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo $collapseId; ?>" aria-expanded="false" aria-controls="<?php echo $collapseId; ?>">
                                    <?php echo $t->name; ?>
                                </button>
                            </h2>
                            <div id="<?php echo $collapseId; ?>" class="accordion-collapse collapse" aria-labelledby="<?php echo $collapseId; ?>_h" data-bs-parent="#sipri-partners-accordion">
                                <div class="accordion-body">
                                    <?php if (isset($t->departments) && is_array($t->departments) && count($t->departments)) { ?>
                                        <ul class="mb0">
                                            <?php foreach ($t->departments as $d) { ?>
                                                <li><?php echo $d->name; ?></li>
                                            <?php } ?>
                                        </ul>
                                    <?php } else { ?>
                                        <div class="text-off"><?php echo app_lang("no_data_found"); ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (!count($partner_tenants)) { ?>
                        <div class="text-center text-off"><?php echo app_lang("no_data_found"); ?></div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="text-off mb10"><?php echo app_lang("sipri_colleagues_hint"); ?></div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th><?php echo app_lang("name"); ?></th>
                                <th><?php echo app_lang("job_title"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($colleagues as $u) { ?>
                                <tr>
                                    <td>
                                        <span class="avatar avatar-xs me-2"><img src="<?php echo get_avatar($u->image); ?>" /></span>
                                        <?php echo $u->first_name . " " . $u->last_name; ?>
                                    </td>
                                    <td><?php echo $u->sipri_position ? $u->sipri_position : "—"; ?></td>
                                </tr>
                            <?php } ?>
                            <?php if (!count($colleagues)) { ?>
                                <tr><td colspan="2" class="text-center text-off"><?php echo app_lang("no_data_found"); ?></td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    feather.replace();
</script>


