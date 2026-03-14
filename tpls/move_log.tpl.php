<?php $self = BNT\Controller\MoveController::as($self); ?>
<?= include_header(); ?>
<?php foreach ($self->messages as $message): ?>
    <div class="alert alert-danger" role="alert">
        <?php if (!is_string($message)) : ?><?php \BNT\Translate::as($message)->l($self->l); ?><?php endif; ?>
        <?= $message; ?>
    </div>
<?php endforeach; ?>
<?= include_footer(); ?>
