<div class="container-fluid">
    <h2 class="mb-4">User Editor</h2>
    
    <form action="admin.php" method="POST">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Player name</label>
                        <input type="text" class="form-control" name="character_name" 
                               value="<?php echo isset($row['character_name']) ? htmlspecialchars($row['character_name'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <input type="text" class="form-control" name="password2" 
                               value="<?php echo isset($row['password']) ? htmlspecialchars($row['password'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">E-mail</label>
                        <input type="email" class="form-control" name="email" 
                               value="<?php echo isset($row['email']) ? htmlspecialchars($row['email'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">ID</label>
                        <div class="form-control bg-light">
                            <?php echo isset($user) ? htmlspecialchars($user, ENT_QUOTES) : ''; ?>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Ship</label>
                        <input type="text" class="form-control" name="ship_name" 
                               value="<?php echo isset($row['ship_name']) ? htmlspecialchars($row['ship_name'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-6 mb-3 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="ship_destroyed" value="ON" 
                                   <?php echo CHECKED($row['ship_destroyed']); ?>>
                            <label class="form-check-label fw-bold">Destroyed?</label>
                        </div>
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
                        <input type="number" class="form-control" name="hull" size="5" 
                               value="<?php echo isset($row['hull']) ? htmlspecialchars($row['hull'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Engines</label>
                        <input type="number" class="form-control" name="engines" size="5" 
                               value="<?php echo isset($row['engines']) ? htmlspecialchars($row['engines'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Power</label>
                        <input type="number" class="form-control" name="power" size="5" 
                               value="<?php echo isset($row['power']) ? htmlspecialchars($row['power'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Computer</label>
                        <input type="number" class="form-control" name="computer" size="5" 
                               value="<?php echo isset($row['computer']) ? htmlspecialchars($row['computer'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Sensors</label>
                        <input type="number" class="form-control" name="sensors" size="5" 
                               value="<?php echo isset($row['sensors']) ? htmlspecialchars($row['sensors'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Armour</label>
                        <input type="number" class="form-control" name="armor" size="5" 
                               value="<?php echo isset($row['armor']) ? htmlspecialchars($row['armor'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Shields</label>
                        <input type="number" class="form-control" name="shields" size="5" 
                               value="<?php echo isset($row['shields']) ? htmlspecialchars($row['shields'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Beams</label>
                        <input type="number" class="form-control" name="beams" size="5" 
                               value="<?php echo isset($row['beams']) ? htmlspecialchars($row['beams'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Torpedoes</label>
                        <input type="number" class="form-control" name="torp_launchers" size="5" 
                               value="<?php echo isset($row['torp_launchers']) ? htmlspecialchars($row['torp_launchers'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">Cloak</label>
                        <input type="number" class="form-control" name="cloak" size="5" 
                               value="<?php echo isset($row['cloak']) ? htmlspecialchars($row['cloak'], ENT_QUOTES) : ''; ?>">
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
                        <input type="number" class="form-control" name="ship_ore" size="8" 
                               value="<?php echo isset($row['ship_ore']) ? htmlspecialchars($row['ship_ore'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Organics</label>
                        <input type="number" class="form-control" name="ship_organics" size="8" 
                               value="<?php echo isset($row['ship_organics']) ? htmlspecialchars($row['ship_organics'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Goods</label>
                        <input type="number" class="form-control" name="ship_goods" size="8" 
                               value="<?php echo isset($row['ship_goods']) ? htmlspecialchars($row['ship_goods'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Energy</label>
                        <input type="number" class="form-control" name="ship_energy" size="8" 
                               value="<?php echo isset($row['ship_energy']) ? htmlspecialchars($row['ship_energy'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Colonists</label>
                        <input type="number" class="form-control" name="ship_colonists" size="8" 
                               value="<?php echo isset($row['ship_colonists']) ? htmlspecialchars($row['ship_colonists'], ENT_QUOTES) : ''; ?>">
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
                        <input type="number" class="form-control" name="ship_fighters" size="8" 
                               value="<?php echo isset($row['ship_fighters']) ? htmlspecialchars($row['ship_fighters'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Torpedoes</label>
                        <input type="number" class="form-control" name="torps" size="8" 
                               value="<?php echo isset($row['torps']) ? htmlspecialchars($row['torps'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Armour Pts</label>
                        <input type="number" class="form-control" name="armor_pts" size="8" 
                               value="<?php echo isset($row['armor_pts']) ? htmlspecialchars($row['armor_pts'], ENT_QUOTES) : ''; ?>">
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
                        <input type="number" class="form-control" name="dev_beacon" size="5" 
                               value="<?php echo isset($row['dev_beacon']) ? htmlspecialchars($row['dev_beacon'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Warp Editors</label>
                        <input type="number" class="form-control" name="dev_warpedit" size="5" 
                               value="<?php echo isset($row['dev_warpedit']) ? htmlspecialchars($row['dev_warpedit'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Genesis Torpedoes</label>
                        <input type="number" class="form-control" name="dev_genesis" size="5" 
                               value="<?php echo isset($row['dev_genesis']) ? htmlspecialchars($row['dev_genesis'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Mine Deflectors</label>
                        <input type="number" class="form-control" name="dev_minedeflector" size="5" 
                               value="<?php echo isset($row['dev_minedeflector']) ? htmlspecialchars($row['dev_minedeflector'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Emergency Warp</label>
                        <input type="number" class="form-control" name="dev_emerwarp" size="5" 
                               value="<?php echo isset($row['dev_emerwarp']) ? htmlspecialchars($row['dev_emerwarp'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="dev_escapepod" value="ON" 
                                   <?php echo CHECKED($row['dev_escapepod']); ?>>
                            <label class="form-check-label">Escape Pod</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-2 d-flex align-items-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="dev_fuelscoop" value="ON" 
                                   <?php echo CHECKED($row['dev_fuelscoop']); ?>>
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
                        <input type="number" class="form-control" name="credits" 
                               value="<?php echo isset($row['credits']) ? htmlspecialchars($row['credits'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Turns</label>
                        <input type="number" class="form-control" name="turns" 
                               value="<?php echo isset($row['turns']) ? htmlspecialchars($row['turns'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-bold">Current sector</label>
                        <input type="number" class="form-control" name="sector" 
                               value="<?php echo isset($row['sector']) ? htmlspecialchars($row['sector'], ENT_QUOTES) : ''; ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <input type="hidden" name="user" value="<?php echo isset($user) ? htmlspecialchars($user, ENT_QUOTES) : ''; ?>">
        <input type="hidden" name="operation" value="save">
        <input type="hidden" name="menu" value="useredit">
        <input type="hidden" name="swordfish" value="<?php echo isset($swordfish) ? htmlspecialchars($swordfish, ENT_QUOTES) : ''; ?>">
        
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-save me-2"></i>Save User
            </button>
        </div>
    </form>
</div>