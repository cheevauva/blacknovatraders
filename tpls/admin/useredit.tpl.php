<?php $title = "Administration"; ?>
<?php include 'header.php'; ?>
<div class="container-fluid">
    <h2 class="mb-4">User Editor</h2>

    <form action="admin.php?module=useredit&user=<?php echo $user; ?>&operation=save" id="bntUsereditForm" method="POST">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Player name</label>
                        <input type="text" class="form-control" name="character_name" value="<?php echo htmlspecialchars($row['character_name']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <input type="text" class="form-control" name="password2" value="">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">E-mail</label>
                        <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">ID</label>
                        <div class="form-control bg-light">
                            <?php echo $user; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ship</label>
                        <input type="text" class="form-control" name="ship_name" value="<?php echo htmlspecialchars($row['ship_name']); ?>">
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ship_destroyed" value="ON" <?php echo CHECKED($row['ship_destroyed']); ?>>
                            <label class="form-check-label fw-bold">Destroyed?</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold mb-2">Role:</label>
                        <select size="1" class="form-select" name="role">
                            <?php echo options(['admin' => 'admin', 'user' => 'user'], $row['role']); ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Ship Levels</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Hull</label>
                        <input type="number" class="form-control" name="hull" size="5" value="<?php echo intval($row['hull']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Engines</label>
                        <input type="number" class="form-control" name="engines" size="5" value="<?php echo intval($row['engines']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Power</label>
                        <input type="number" class="form-control" name="power" size="5" value="<?php echo intval($row['power']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Computer</label>
                        <input type="number" class="form-control" name="computer" size="5" value="<?php echo intval($row['computer']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Sensors</label>
                        <input type="number" class="form-control" name="sensors" size="5" value="<?php echo intval($row['sensors']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Armour</label>
                        <input type="number" class="form-control" name="armor" size="5" value="<?php echo intval($row['armor']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Shields</label>
                        <input type="number" class="form-control" name="shields" size="5" value="<?php echo intval($row['shields']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Beams</label>
                        <input type="number" class="form-control" name="beams" size="5" value="<?php echo intval($row['beams']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Torpedoes</label>
                        <input type="number" class="form-control" name="torp_launchers" size="5" value="<?php echo intval($row['torp_launchers']); ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Cloak</label>
                        <input type="number" class="form-control" name="cloak" size="5" value="<?php echo intval($row['cloak']); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Ship Holds</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Ore</label>
                        <input type="number" class="form-control" name="ship_ore" size="8" value="<?php echo intval($row['ship_ore']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Organics</label>
                        <input type="number" class="form-control" name="ship_organics" size="8" value="<?php echo intval($row['ship_organics']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Goods</label>
                        <input type="number" class="form-control" name="ship_goods" size="8" value="<?php echo intval($row['ship_goods']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Energy</label>
                        <input type="number" class="form-control" name="ship_energy" size="8" value="<?php echo intval($row['ship_energy']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Colonists</label>
                        <input type="number" class="form-control" name="ship_colonists" size="8" value="<?php echo intval($row['ship_colonists']); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Combat</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Fighters</label>
                        <input type="number" class="form-control" name="ship_fighters" size="8" value="<?php echo intval($row['ship_fighters']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Torpedoes</label>
                        <input type="number" class="form-control" name="torps" size="8" value="<?php echo intval($row['torps']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Armour Pts</label>
                        <input type="number" class="form-control" name="armor_pts" size="8" value="<?php echo intval($row['armor_pts']); ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Devices</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Beacons</label>
                        <input type="number" class="form-control" name="dev_beacon" size="5" value="<?php echo intval($row['dev_beacon']); ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Warp Editors</label>
                        <input type="number" class="form-control" name="dev_warpedit" size="5" value="<?php echo intval($row['dev_warpedit']); ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Genesis Torpedoes</label>
                        <input type="number" class="form-control" name="dev_genesis" size="5" value="<?php echo intval($row['dev_genesis']); ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Mine Deflectors</label>
                        <input type="number" class="form-control" name="dev_minedeflector" size="5" value="<?php echo intval($row['dev_minedeflector']) ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Emergency Warp</label>
                        <input type="number" class="form-control" name="dev_emerwarp" size="5" value="<?php echo intval($row['dev_emerwarp']);?>">
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="dev_escapepod" value="ON"  <?php echo CHECKED($row['dev_escapepod']); ?>>
                            <label class="form-check-label">Escape Pod</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="dev_fuelscoop" value="ON" <?php echo CHECKED($row['dev_fuelscoop']); ?>>
                            <label class="form-check-label">FuelScoop</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Resources & Location</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Credits</label>
                        <input type="number" class="form-control" name="credits" value="<?php echo intval($row['credits']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Turns</label>
                        <input type="number" class="form-control" name="turns" value="<?php echo intval($row['turns']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Current sector</label>
                        <input type="number" class="form-control" name="sector" value="<?php echo intval($row['sector']); ?>">
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
<?php include 'footer.php'; ?>
