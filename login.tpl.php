<?php include("header.php"); ?>

<?php bigtitle(); ?>

<form action="login2.php" method="post">
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label"><?php echo $l_login_email; ?></label>
        <input type="text" name="email" class="form-control" aria-describedby="emailHelp" required>
        <div id="emailHelp" class="form-text"></div>
    </div>
    <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label"><?php echo $l_login_pw; ?></label>
        <input type="password" name="pass" class="form-control" required>
    </div>
    <div class="mb-3 row">
        <div class="form-text">
            <?php echo $l_login_newp; ?>
        </div>
    </div>
    <button type="submit" class="btn btn-primary"><?php echo $l_login_title; ?></button>
</form>

<?php include("footer.php"); ?>
