<?php include "header.php"; ?>
<div class="container mt-4">
    <form action="schema.php" method="post" class="row g-3">
        <div class="col-auto">
            <input type="password"  class="form-control"  id="password"  name="password" size="20"  maxlength="20" placeholder="<?php echo $l_schema_password;?>">
        </div>

        <div class="col-auto">
            <input type="submit"  class="btn btn-primary" value="<?php echo $l_submit;?>">
        </div>
        <div class="col-auto">
            <input type="reset" class="btn btn-secondary"  value="<?php echo $l_reset;?>">
        </div>
    </form>
</div>
<?php include "footer.php"; ?>
