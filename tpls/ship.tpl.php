<?php $self = BNT\Controller\ShipController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<?php if ($self->othership['sector'] != $self->playerinfo['sector']) : ?>
    <div class="alert alert-info">
        <?= $l->l_ship_the; ?> <?= $self->othership['ship_name']; ?> <?= $l->l_ship_nolonger; ?> <?= $playerinfo['sector']; ?>
    </div>
<?php else : ?>
    <div class="mb-3">
        <div class="alert alert-warning">
            <?= $l->l_ship_youc; ?> 
            <?= htmlspecialchars($self->othership['ship_name']); ?>
        </div>
    </div>
    <div class="mb-3">
        <?= $l->l_ship_perform; ?>
    </div>
    <div class="mb-3">
        <a class="btn btn-secondary" href="<?= route('scan', ['ship_id' => $self->ship_id]); ?>" ><?= $l->l_planet_scn_link; ?></a>
    </div>
    <div class="mb-3">
        <a class="btn btn-danger" href="<?= route('attack', ['ship_id' => $self->ship_id]); ?>"><?= $l->l_planet_att_link; ?></a>
    </div>
    <div class="mb-3">
        <a class="btn btn-info" href="<?= route('messages', ['ship' => $self->ship_id, 'send' => 1]); ?>"><?= $l->l_send_msg; ?></a>
    </div>
<?php endif; ?>
<?php include_footer(); ?>
