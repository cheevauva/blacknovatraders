<?php $self = BNT\Controller\DeviceController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<?= $l->l_device_expl; ?>
<BR><BR>
<TABLE class="table table-hover">
    <TR>
        <TD><B><?= $l->l_device; ?></B></TD>
        <TD><B><?= $l->l_qty; ?></B></TD>
        <TD><B><?= $l->l_usage; ?></B></TD>
    </TR>
    <TR>
        <TD><a href="<?= route('beacon'); ?>"><?= $l->l_beacons; ?></A></TD>
        <TD><?= number($self->playerinfo['dev_beacon']); ?></TD>
        <TD><?= $l->l_manual; ?></TD>
    </TR>
    <TR >
        <TD><a href="<?= route('warpedit'); ?>"><?= $l->l_warpedit; ?></A></TD>
        <TD><?= number($self->playerinfo['dev_warpedit']); ?></TD>
        <TD><?= $l->l_manual; ?></TD>
    </TR>
    <TR>
        <TD><a href="<?= route('genesis');?>"><?= $l->l_genesis; ?></A></TD>
        <TD><?= number($self->playerinfo['dev_genesis']); ?></TD>
        <TD><?= $l->l_manual; ?></TD>
    </TR>
    <TR >
        <TD><?= $l->l_deflect; ?></TD>
        <TD><?= number($self->playerinfo['dev_minedeflector']); ?></TD>
        <TD><?= $l->l_automatic; ?></TD>
    </TR>
    <TR>
        <TD><a href="<?= route('mines', 'op=1'); ?>"><?= $l->l_mines; ?></A></TD>
        <TD><?= number($self->playerinfo['torps']); ?></TD>
        <TD><?= $l->l_manual; ?></TD>
    </TR>
    <TR >
        <TD><a href="<?= route('mines', 'op=1'); ?>"><?= $l->l_fighters; ?></A></TD>
        <TD><?= number($self->playerinfo['ship_fighters']); ?></TD>
        <TD><?= $l->l_manual; ?></TD>
    </TR>
    <TR>
        <TD><a href="<?= route('emerwarp'); ?>"><?= $l->l_ewd; ?></A></TD>
        <TD><?= number($self->playerinfo['dev_emerwarp']); ?></TD>
        <TD><?= $l->l_manual; ?>/<?= $l->l_automatic; ?></TD>
    </TR>
    <TR >
        <TD><?= $l->l_escape_pod; ?></TD>
        <TD>
            <?= lYesNo($self->playerinfo['dev_escapepod']); ?>
        </TD>
        <TD><?= $l->l_automatic; ?></TD>
    </TR>
    <TR>
        <TD><?= $l->l_fuel_scoop; ?></TD>
        <TD>
            <?= lYesNo($self->playerinfo['dev_fuelscoop']); ?>
        </TD>
        <TD><?= $l->l_automatic; ?></TD>
    </TR>
    <TR >
        <TD><?= $l->l_lssd; ?></TD>
        <TD>
            <?= lYesNo($self->playerinfo['dev_lssd']); ?>
        </TD>
        <TD><?= $l->l_automatic; ?></TD>
    </TR>
</TABLE>
<BR>
<?php include_footer(); ?>
