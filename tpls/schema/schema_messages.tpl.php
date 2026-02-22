<?php
global $l_schema_title;
$title = $l_schema_title;
$self = BNT\Controller\SchemaController::as($self);
?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<ul class="list-group">
    <?php foreach ($self->messages as $message) : ?>
        <li class="list-group-item"><?php echo $message; ?></li>
    <?php endforeach; ?>
</ul>
<?php include_footer(); ?>
