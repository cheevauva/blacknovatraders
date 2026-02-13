<?php include "header.php"; ?>
<div class="alert alert-info mb-4" role="alert">
    Create Universe Confirmation [So you would like your <?php echo $sector_max; ?> sector universe to have]
</div>
<?php if ($fedsecs > $sector_max) : ?>
    <div class="alert alert-danger mb-4" role="alert">
        The number of Federation sectors must be smaller than the size of the universe!
    </div>
    <hr class="my-4">
<?php endif; ?>

<form action="create_universe.php" method="post" class="form-container">
    <input type="hidden" name="step" value="3">
    <input type="hidden" name="spp" value="<?php echo (int) $specialSectorsCount; ?>">
    <input type="hidden" name="spp" value="<?php echo (int) $specialSectorsCount; ?>">
    <input type="hidden" name="oep" value="<?php echo (int) $oreSectorsCount; ?>">
    <input type="hidden" name="ogp" value="<?php echo (int) $organicsSectorsCount; ?>">
    <input type="hidden" name="gop" value="<?php echo (int) $goodsSectorsCount; ?>">
    <input type="hidden" name="enp" value="<?php echo (int) $energySectorsCount; ?>">
    <input type="hidden" name="initscommod" value="<?php echo (int) $initscommod; ?>">
    <input type="hidden" name="initbcommod" value="<?php echo (int) $initbcommod; ?>">
    <input type="hidden" name="nump" value="<?php echo (int) $unownedPlanetsCount; ?>">
    <input type="hidden" name="fedsecs" value="<?php echo (int) $fedsecs; ?>">
    <input type="hidden" name="engage" value="3">
    <input type="hidden" name="swordfish" value="<?php echo $swordfish; ?>">
    <div class="mb-4">
        <h4 class="mb-3">Configuration Summary</h4>
        
        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Universe size</div>
            <div class="col-md-6"><?php echo (int) $universe_size; ?></div>
        </div>
        
        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Special ports</div>
            <div class="col-md-6"><?php echo intval($specialSectorsCount); ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Ore ports</div>
            <div class="col-md-6"><?php echo intval($oreSectorsCount); ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Organics ports</div>
            <div class="col-md-6"><?php echo intval($organicsSectorsCount); ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Goods ports</div>
            <div class="col-md-6"><?php echo intval($goodsSectorsCount); ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 fw-bold">Energy ports</div>
            <div class="col-md-6"><?php echo intval($energySectorsCount); ?></div>
        </div>

        <hr class="my-3">

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Initial commodities to sell</div>
            <div class="col-md-6"><?php echo number_format($initscommod, 2); ?>%</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 fw-bold">Initial commodities to buy</div>
            <div class="col-md-6"><?php echo number_format($initbcommod, 2); ?>%</div>
        </div>

        <hr class="my-3">

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Empty sectors</div>
            <div class="col-md-6"><?php echo intval($empty); ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Federation sectors</div>
            <div class="col-md-6"><?php echo intval($fedsecs); ?></div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6 fw-bold">Unowned planets</div>
            <div class="col-md-6"><?php echo intval($unownedPlanetsCount); ?></div>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex  mb-4">
        <input type="submit" class="btn btn-primary" value="Confirm">
    </div>
</form>
<?php include "footer.php"; ?>
