<?php $self = BNT\Controller\DeviceController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<?= $l->device_expl; ?>
<BR><BR>
<TABLE class="table table-hover">
    <TR>
        <TD><B><?= $l->device; ?></B></TD>
        <TD><B><?= $l->qty; ?></B></TD>
        <TD><B><?= $l->usage; ?></B></TD>
    </TR>
    <TR>
        <TD><a href="<?= route('beacon'); ?>"><?= $l->beacons; ?></A></TD>
        <TD><?= number($self->playerinfo['dev_beacon']); ?></TD>
        <TD><?= $l->manual; ?></TD>
    </TR>
    <TR >
        <TD><a href="<?= route('warpedit'); ?>"><?= $l->warpedit; ?></A></TD>
        <TD><?= number($self->playerinfo['dev_warpedit']); ?></TD>
        <TD><?= $l->manual; ?></TD>
    </TR>
    <TR>
        <TD><a href="<?= route('genesis');?>"><?= $l->genesis; ?></A></TD>
        <TD><?= number($self->playerinfo['dev_genesis']); ?></TD>
        <TD><?= $l->manual; ?></TD>
    </TR>
    <TR >
        <TD><?= $l->deflect; ?></TD>
        <TD><?= number($self->playerinfo['dev_minedeflector']); ?></TD>
        <TD><?= $l->automatic; ?></TD>
    </TR>
    <TR>
        <TD><a href="<?= route('mines', 'op=1'); ?>"><?= $l->mines; ?></A></TD>
        <TD><?= number($self->playerinfo['torps']); ?></TD>
        <TD><?= $l->manual; ?></TD>
    </TR>
    <TR >
        <TD><a href="<?= route('mines', 'op=1'); ?>"><?= $l->fighters; ?></A></TD>
        <TD><?= number($self->playerinfo['ship_fighters']); ?></TD>
        <TD><?= $l->manual; ?></TD>
    </TR>
    <TR>
        <TD><a href="<?= route('emerwarp'); ?>"><?= $l->ewd; ?></A></TD>
        <TD><?= number($self->playerinfo['dev_emerwarp']); ?></TD>
        <TD><?= $l->manual; ?>/<?= $l->automatic; ?></TD>
    </TR>
    <TR >
        <TD><?= $l->escape_pod; ?></TD>
        <TD>
            <?= lYesNo($self->playerinfo['dev_escapepod']); ?>
        </TD>
        <TD><?= $l->automatic; ?></TD>
    </TR>
    <TR>
        <TD><?= $l->fuel_scoop; ?></TD>
        <TD>
            <?= lYesNo($self->playerinfo['dev_fuelscoop']); ?>
        </TD>
        <TD><?= $l->automatic; ?></TD>
    </TR>
    <TR >
        <TD><?= $l->lssd; ?></TD>
        <TD>
            <?= lYesNo($self->playerinfo['dev_lssd']); ?>
        </TD>
        <TD><?= $l->automatic; ?></TD>
    </TR>
</TABLE>
<BR>
<?php include_footer(); ?>
