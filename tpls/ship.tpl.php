<?php $self = BNT\Controller\ShipController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<?php if ($self->othership['sector'] != $self->playerinfo['sector']) : ?>
    <div class="alert alert-info">
        <?= $l->ship_the; ?> <?= $self->othership['ship_name']; ?> <?= $l->ship_nolonger; ?> <?= $playerinfo['sector']; ?>
    </div>
<?php else : ?>
    <div class="mb-3">
        <div class="alert alert-warning">
            <?= $l->ship_youc; ?> 
            <?= htmlspecialchars($self->othership['ship_name']); ?>
        </div>
    </div>
    <div class="mb-3">
        <?= $l->ship_perform; ?>
    </div>
    <div class="mb-3">
        <a class="btn btn-secondary" href="scan.php?ship_id=<?= $self->ship_id; ?>" ><?= $l->planet_scn_link; ?></a>
    </div>
    <div class="mb-3">
        <a class="btn btn-danger" href="attack.php?ship_id=<?= $self->ship_id; ?>"><?= $l->planet_att_link; ?></a>
    </div>
    <div class="mb-3">
        <a class="btn btn-info" href="mailto.php?to=<?= $self->ship_id; ?>"><?= $l->send_msg; ?></a>
    </div>
<?php endif; ?>
<?php include_footer(); ?>
