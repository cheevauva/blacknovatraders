<div class="card shadow">
    <div class="card-header bg-danger text-white">
        <h4 class="mb-0">
            Universe Editor
        </h4>
    </div>
    <div class="card-body">
        <h5 class="text-danger mb-3">
            Expand/Contract the Universe
        </h5>
        <form action="<?= route('admin', 'module=univedit&operation=doexpand'); ?>" method="POST">
            <div class="row align-items-center mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        Universe Size:
                    </label>
                </div>
                <div class="col-md-6">
                    <input type="number"  class="form-control form-control-lg"  name="radius"   value="<?php echo intval($universe_size); ?>"  min="1" required>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-danger btn-lg px-5">
                    Play God
                </button>
            </div>
        </form>
    </div>
    <div class="card-footer bg-light">
        <small class="text-muted">
            <i class="bi bi-exclamation-diamond me-1"></i>
            Warning: This action will modify the entire game universe structure.
        </small>
    </div>
</div>
