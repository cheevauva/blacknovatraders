<?php $self = BNT\Controller\NavCompController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<form action="<?= route('navcomp');?>" method="POST" class="navcomp-form" id="bntNavCompForm">
    <div class="row g-3 align-items-center">
        <div class="col-auto">
            <label for="stop_sector" class="col-form-label"><?= $l->nav_query; ?></label>
        </div>
        <div class="col-auto">
            <input type="text" name="stop_sector" id="stop_sector" class="form-control" placeholder="<?= $l->nav_query; ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><?= $l->submit; ?></button>
        </div>
    </div>

    <input type="hidden" name="state" value="1">
</form>
<script type="text/javascript">
    bntForm('bntNavCompForm');
</script>
<?php include_footer(); ?>
