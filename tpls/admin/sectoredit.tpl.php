<div class="container-fluid mt-4">
    <h2 class="mb-4">
        <i class="bi bi-pin-map me-2"></i>Sector Editor
    </h2>
    
    <form action="admin.php" method="POST">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Sector Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Sector ID</span>
                        </label>
                        <div class="form-control bg-light">
                            <span class="text-success"><?php echo isset($sector) ? htmlspecialchars($sector, ENT_QUOTES) : ''; ?></span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Sector Name</span>
                        </label>
                        <input type="text" class="form-control" name="sector_name" size="15" 
                               value="<?php echo isset($row['sector_name']) ? htmlspecialchars($row['sector_name'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Zone ID</span>
                        </label>
                        <select class="form-select" size="1" name="zone_id">
                            <?php echo options($zones, isset($row['zone_id']) ? $row['zone_id'] : null); ?>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-12">
                        <label class="form-label">
                            <span class="font-monospace">Beacon</span>
                        </label>
                        <input type="text" class="form-control" name="beacon" size="70" 
                               value="<?php echo isset($row['beacon']) ? htmlspecialchars($row['beacon'], ENT_QUOTES) : ''; ?>">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">
                            <span class="font-monospace">Distance</span>
                        </label>
                        <input type="number" class="form-control" name="distance" size="9" 
                               value="<?php echo isset($row['distance']) ? htmlspecialchars($row['distance'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <span class="font-monospace">Angle1</span>
                        </label>
                        <input type="number" class="form-control" name="angle1" size="9" 
                               value="<?php echo isset($row['angle1']) ? htmlspecialchars($row['angle1'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">
                            <span class="font-monospace">Angle2</span>
                        </label>
                        <input type="number" class="form-control" name="angle2" size="9" 
                               value="<?php echo isset($row['angle2']) ? htmlspecialchars($row['angle2'], ENT_QUOTES) : ''; ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Port Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Port Type</span>
                        </label>
                        <select class="form-select" size="1" name="port_type">
                            <?php echo options($port_types, isset($row['port_type']) ? $row['port_type'] : null); ?>
                            <option value="none" <?php echo isset($oportnon) && $oportnon == 'SELECTED=none VALUE' ? 'selected' : ''; ?>>none</option>
                            <option value="organics" <?php echo isset($oportorg) && $oportorg == 'SELECTED=organics VALUE' ? 'selected' : ''; ?>>organics</option>
                            <option value="ore" <?php echo isset($oportore) && $oportore == 'SELECTED=ore VALUE' ? 'selected' : ''; ?>>ore</option>
                            <option value="goods" <?php echo isset($oportgoo) && $oportgoo == 'SELECTED=goods VALUE' ? 'selected' : ''; ?>>goods</option>
                            <option value="energy" <?php echo isset($oportene) && $oportene == 'SELECTED=energy VALUE' ? 'selected' : ''; ?>>energy</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Organics</span>
                        </label>
                        <input type="number" class="form-control" name="port_organics" size="9" 
                               value="<?php echo isset($row['port_organics']) ? htmlspecialchars($row['port_organics'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Ore</span>
                        </label>
                        <input type="number" class="form-control" name="port_ore" size="9" 
                               value="<?php echo isset($row['port_ore']) ? htmlspecialchars($row['port_ore'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">
                            <span class="font-monospace">Goods</span>
                        </label>
                        <input type="number" class="form-control" name="port_goods" size="9" 
                               value="<?php echo isset($row['port_goods']) ? htmlspecialchars($row['port_goods'], ENT_QUOTES) : ''; ?>">
                    </div>
                    <div class="col-md-3 mt-3">
                        <label class="form-label">
                            <span class="font-monospace">Energy</span>
                        </label>
                        <input type="number" class="form-control" name="port_energy" size="9" 
                               value="<?php echo isset($row['port_energy']) ? htmlspecialchars($row['port_energy'], ENT_QUOTES) : ''; ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <input type="hidden" name="sector" value="<?php echo isset($sector) ? htmlspecialchars($sector, ENT_QUOTES) : ''; ?>">
        <input type="hidden" name="operation" value="save">
        <input type="hidden" name="menu" value="sectedit">
        <input type="hidden" name="swordfish" value="<?php echo isset($swordfish) ? htmlspecialchars($swordfish, ENT_QUOTES) : ''; ?>">
        
        <div class="text-center">
            <button type="submit" class="btn btn-primary btn-lg px-5">
                <i class="bi bi-save me-2"></i>Save Sector
            </button>
        </div>
    </form>
</div>