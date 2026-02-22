<?php include_header(); ?>
<?php $self = BNT\Controller\BaseController::as($self); ?>
<div class="alert alert-danger">
    <?php echo $self->exception?->getMessage(); ?>
</div>
<?php include_footer(); ?>
