<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0">
            Zone Editor
        </h3>
    </div>
    <div class="card-body">
        <form action="admin.php" method="POST">
            <div class="row align-items-start">
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold mb-2">Select Zone:</label>
                    <select size="10" class="form-select h-100" name="zone">
                        <?php echo options($zones, null); ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3 d-flex flex-column align-items-center">
                    <div class="mt-4 pt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            Edit Zone
                        </button>
                    </div>
                </div>
            </div>

            <input type="hidden" name="operation" value="editzone">
            <input type="hidden" name="menu" value="zoneedit">
            <input type="hidden" name="swordfish" value="<?php echo isset($swordfish) ? htmlspecialchars($swordfish, ENT_QUOTES) : ''; ?>">
        </form>
    </div>
    <div class="card-footer bg-light">
        <small class="text-muted">
            Select a zone from the list to edit its properties and permissions.
        </small>
    </div>
</div>