<?php
global $l_schema_title;
$title = $l_schema_title;
$self = BNT\Controller\SchemaController::as($this);
?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<?php foreach ($self->messages as $message) : ?>
    <div class="alert alert-info">
        <?php echo $message; ?>
    </div>
<?php endforeach; ?>
<?php include_footer(); ?>
