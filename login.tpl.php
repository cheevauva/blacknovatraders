<?php include("header.php"); ?>

<?php bigtitle(); ?>

<form action="login2.php" id="bntLoginForm" method="post">
    <div class="alert alert-warning alert-dismissible bntLoginError d-none" role="alert">
        <span class="error"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
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
    document.getElementById('bntLoginForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);

        const response = await fetch('login2.php', {
            method: 'POST',
            body: formData
        });

        if (response.redirected) {
            window.location.href = response.url;
            return;
        }

        const result = await response.json();

        document.getElementById('bntLoginForm').getElementsByClassName('bntLoginError')[0].classList.remove('d-none');
        document.getElementById('bntLoginForm').getElementsByClassName('bntLoginError')[0].getElementsByClassName('error')[0].innerHTML = result.error;
    });
</script>

<?php include("footer.php"); ?>
