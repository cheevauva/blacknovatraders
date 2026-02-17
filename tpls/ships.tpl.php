<?php 
global $userinfo;
$self = \BNT\Controller\ShipsController::as($this);
?>
<?php include_header(); ?>
<div class="container mt-4">
    <form action="ships.php" method="post" class="row g-3">
        <div class="col-auto">
            <select name="ship_id" class="form-select w-auto"  id="ship_id">
                <?php echo options($self->ships, $userinfo['ship_id'] ?? null);?>
            </select>
        </div>

        <div class="col-auto">
            <input type="submit"  class="btn btn-primary" value="<?php echo $l->submit; ?>">
        </div>
        <div class="col-auto">
            <input type="reset" class="btn btn-secondary"  value="<?php echo $l->reset; ?>">
        </div>
    </form>
</div>
<?php include_footer(); ?>
