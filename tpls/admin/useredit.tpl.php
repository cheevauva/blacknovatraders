<?php $self = BNT\Controller\AdminUserController::as($this); ?>
<?php $user = $self->user; ?>
<?php include_header(); ?>
<div class="container-fluid">
    <h2 class="mb-4">Ship Editor</h2>

    <form action="admin.php?module=user&operation=save&user=<?php echo $user['id']; ?>" id="bntUsereditForm" method="POST">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Player name</label>
                        <input type="text" class="form-control" name="character_name" value="<?php echo htmlspecialchars($user['character_name']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" class="form-control" name="password" value="">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">E-mail</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">ID</label>
                        <div class="form-control bg-light">
                            <?php echo $user['id']; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ship</label>
                        <select type="text" class="form-control" name="ship_id">
                            <?= options($self->ships, $user['ship_id']); ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold mb-2">Role:</label>
                        <select size="1" class="form-select" name="role">
                            <?php echo options(['admin' => 'admin', 'user' => 'user'], $user['role']); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-save me-2"></i>Save User
        </button>
    </form>
    <script type="text/javascript">
        bntForm('bntUsereditForm');
    </script>
</div>
<?php include_footer(); ?>
