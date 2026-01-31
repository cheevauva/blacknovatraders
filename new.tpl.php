<?php $title = $l_new_title; ?>
<?php include("header.php"); ?>
<?php bigtitle(); ?>
<form action="new2.php" id="bntNewForm" method="post">
    <div class="alert alert-warning alert-dismissible bntNewError d-none" role="alert">
        <span class="error"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <div class="mb-3">
        <div class="form-text">
            <? echo $l_new_info; ?>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label"><?php echo $l_login_email; ?></label>
        <input type="text" name="username" class="form-control" maxlength="40" required>
    </div>
    <div class="mb-3">
        <label class="form-label"><? echo $l_new_shipname; ?></label>
        <input type="text" name="shipname" class="form-control" maxlength="20"  required>
    </div>
    <div class="mb-3">
        <label class="form-label"><? echo $l_new_pname; ?></label>
        <input type="text" name="character" class="form-control" maxlength="20"  required>
    </div>
    <div class="mb-3">
        <label class="form-label"><? echo $l_new_pname; ?></label>
        <input type="password" name="password" class="form-control" maxlength="20"  required>
    </div>
    <input type="submit" class="btn btn-primary" value="<? echo $l_submit; ?>">
    <input type="reset"  class="btn btn-primary" value="<? echo $l_reset; ?>">
</form>
<script type="text/javascript">
    document.getElementById('bntNewForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);

        const response = await fetch('new2.php', {
            method: 'POST',
            body: formData
        });

        if (response.redirected) {
            window.location.href = response.url;
            return;
        }

        const result = await response.json();

        document.getElementById('bntNewForm').getElementsByClassName('bntNewError')[0].classList.remove('d-none');
        document.getElementById('bntNewForm').getElementsByClassName('bntNewError')[0].getElementsByClassName('error')[0].innerHTML = result.error;
    });
</script>
<? include("footer.php"); ?>
