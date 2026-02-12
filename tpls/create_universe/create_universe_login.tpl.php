<?php include "header.php"; ?>
<div class="container mt-4">
    <form action="create_universe.php" method="post" class="row g-3">
        <div class="col-auto">
            <input type="password"  class="form-control"   id="password"  name="swordfish" size="20"  maxlength="20" placeholder="<?php echo $l_create_universe_password;?>">
        </div>

        <div class="col-auto">
            <input type="submit"  class="btn btn-primary" value="<?php echo $l_submit;?>">
        </div>
        <div class="col-auto">
            <input type="reset" class="btn btn-secondary"  value="<?php echo $l_reset;?>">
        </div>
        <input type="hidden" name="step" value="1"/>
    </form>
</div>
<?php include "footer.php"; ?>
