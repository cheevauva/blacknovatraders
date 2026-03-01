<?php include_header(); ?>
<?php $self = BNT\Controller\BaseController::as($self); ?>
<?php
$type = match (true) {
    $self->exception instanceof ErrorException => 'danger',
    $self->exception instanceof WarningException => 'warning',
    $self->exception instanceof InfoException => 'info',
    $self->exception instanceof SuccessException => 'success',
    default => 'primary',
};
?>
<div class="alert alert-<?= $type;?>">
<?php echo $self->exception?->getMessage(); ?>
</div>
<?php include_footer(); ?>
