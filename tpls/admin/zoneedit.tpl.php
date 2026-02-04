
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h3 class="mb-0">
            Zone Editor
        </h3>
    </div>
    <div class="card-body">
        <form action="admin.php" method="POST">
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">Zone ID</label>
                    <div class="form-control bg-light">
                        <?php echo isset($row['zone_id']) ? htmlspecialchars($row['zone_id'], ENT_QUOTES) : ''; ?>
                    </div>
                </div>
                <div class="col-md-8 mb-3">
                    <label class="form-label fw-bold">Zone Name</label>
                    <input type="text" class="form-control" name="zone_name" 
                           value="<?php echo isset($row['zone_name']) ? htmlspecialchars($row['zone_name'], ENT_QUOTES) : ''; ?>">
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="border-bottom pb-2 mb-3">Zone Permissions</h6>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="zone_beacon" value="ON" 
                               <?php echo CHECKED($row['allow_beacon']); ?>>
                        <label class="form-check-label">Allow Beacon</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="zone_attack" value="ON" 
                               <?php echo CHECKED($row['allow_attack']); ?>>
                        <label class="form-check-label">Allow Attack</label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="zone_warpedit" value="ON" 
                               <?php echo CHECKED($row['allow_warpedit']); ?>>
                        <label class="form-check-label">Allow WarpEdit</label>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="zone_planet" value="ON" 
                               <?php echo CHECKED($row['allow_planet']); ?>>
                        <label class="form-check-label">Allow Planet</label>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Max Hull</label>
                    <input type="number" class="form-control" name="zone_hull" 
                           value="<?php echo isset($row['max_hull']) ? htmlspecialchars($row['max_hull'], ENT_QUOTES) : ''; ?>">
                </div>
            </div>

            <input type="hidden" name="zone" value="<?php echo isset($zone) ? htmlspecialchars($zone, ENT_QUOTES) : ''; ?>">
            <input type="hidden" name="operation" value="savezone">
            <input type="hidden" name="menu" value="zoneedit">
            <input type="hidden" name="swordfish" value="<?php echo isset($swordfish) ? htmlspecialchars($swordfish, ENT_QUOTES) : ''; ?>">

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    Save Zone
                </button>
            </div>
        </form>
    </div>
</div>
