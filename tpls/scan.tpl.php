<?php $self = BNT\Controller\ScanController::as($self); ?>
<?= include_header(); ?>
<?= bigtitle(); ?>
<div class="scan-result">
    <?php if (!empty($self->btyamount)): ?> 
        <div class="alert alert-info"><?= $l->t('l_scan_bounty', ['amount' => $self->btyamount]); ?></div>
    <?php endif; ?>
    <?php if (!empty($self->btyamountfed)): ?> 
        <div class="alert alert-info"><?= $l->l_scan_fedbounty; ?></div>
    <?php endif; ?>

    <?php if (!empty($self->isfedbounty)): ?>
        <div class="alert alert-danger"><?= $l->l_by_fedbounty; ?></div>
    <?php else: ?>
        <div class="alert alert-success"><?= $l->l_by_nofedbounty; ?></div>
    <?php endif; ?>

    <p class="lead"><?= $l->l_scan_ron . " " . htmlspecialchars($self->targetinfo['ship_name']); ?></p>

    <h5 class="mt-4"><?= $l->l_ship_levels; ?></h5>
    <table class="table table-striped table-bordered">
        <tr>
            <td><?= $l->l_hull; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['hull'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_engines; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['engines'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_power; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['power'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_computer; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['computer'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_sensors; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['sensors'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_beams; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['beams'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_torpedo . " " . $l->l_launchers; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['torp_launchers'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_armor; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['armor'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_shields; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['shields'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_cloak; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['cloak'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
    </table>

    <h5 class="mt-4"><?= $l->l_scan_arma; ?></h5>
    <table class="table table-striped table-bordered">
        <tr>
            <td><?= $l->l_armorpts; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['armor_pts'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_fighters; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['ship_fighters'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_torps; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['torps'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
    </table>

    <h5 class="mt-4"><?= $l->l_scan_carry; ?></h5>
    <table class="table table-striped table-bordered">
        <tr>
            <td>Credits:</td>
            <td><?= rand(1, 100) < $self->success ? number_format(round($self->targetinfo['credits'] * $self->sc_error / 100)) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_colonists; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['ship_colonists'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_energy; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['ship_energy'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_ore; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['ship_ore'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_organics; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['ship_organics'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_goods; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['ship_goods'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
    </table>

    <h5 class="mt-4"><?= $l->l_devices; ?></h5>
    <table class="table table-striped table-bordered">
        <tr>
            <td><?= $l->l_warpedit; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['dev_warpedit'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_genesis; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['dev_genesis'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_deflect; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['dev_minedeflector'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_ewd; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? round($self->targetinfo['dev_emerwarp'] * $self->sc_error / 100) : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_escape_pod; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? $self->targetinfo['dev_escapepod'] : "???"; ?></td>
        </tr>
        <tr>
            <td><?= $l->l_fuel_scoop; ?>:</td>
            <td><?= rand(1, 100) < $self->success ? $self->targetinfo['dev_fuelscoop'] : "???"; ?></td>
        </tr>
    </table>
</div>
<?= include_footer(); ?>
