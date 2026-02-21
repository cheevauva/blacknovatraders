<?php $self = BNT\Controller\AdminConfigController::as($this); ?>
<?php $config = $self->config; ?>
<?php include_header(); ?>
<div class="container-fluid">
    <h2 class="mb-4">Ship Editor</h2>

    <form action="admin.php?module=config&operation=save" id="bntConfigEditForm" method="POST">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Configuration</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Secret Pass</label>
                        <input type="password" class="form-control" name="adminpass" value="">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Admin E-mail</label>
                        <input type="text" class="form-control" name="admin_mail" value="<?= htmlspecialchars($config['admin_mail']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">  Universe Size</label>
                        <input type="number"  class="form-control form-control-lg" name="universe_size" value="<?php echo intval($config['universe_size'] ?? $GLOBALS['universe_size']); ?>"  min="1" required>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg px-5">
            Save
        </button>
    </form>
    <script type="text/javascript">
        bntForm('bntConfigEditForm');
    </script>
</div>
<?php include_footer(); ?>
