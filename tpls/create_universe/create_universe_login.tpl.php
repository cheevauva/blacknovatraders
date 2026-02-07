<?php include "header.php"; ?>
<div class="container mt-4">
    <form action="create_universe.php" method="post" class="row g-3">
        <div class="col-auto">
            <label for="password" class="visually-hidden">Password:</label>
            <input type="password"  class="form-control"   id="password"  name="swordfish" size="20"  maxlength="20" placeholder="Password">
        </div>

        <div class="col-auto">
            <input type="submit"  class="btn btn-primary" value="Submit">
        </div>
        <div class="col-auto">
            <input type="reset" class="btn btn-secondary"  value="Reset">
        </div>
        <input type="hidden" name="step" value="1"/>
    </form>
</div>
<?php include "footer.php"; ?>
