<?php 
global $l_login_title;
global $l_login_newp;
global $l_login_pw;
global $l_login_email;

$title = $l_login_title;
?>
<?php include_header();?>
<?php bigtitle(); ?>
<?php if (!empty($ex)) : ?>  
    <div class="alert alert-danger">
        <?php echo $ex->getMessage(); ?>
    </div>
<?php else : ?>
    <form action="login.php" id="bntLoginForm" method="post">
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
    <script type="text/javascript">
        bntForm('bntLoginForm');
    </script>
<?php endif; ?>

<?php include_footer();?>
