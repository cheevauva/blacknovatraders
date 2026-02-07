<?php include "header.php"; ?>
<div class="alert alert-info mb-4" role="alert">
    Create Universe Confirmation [So you would like your <?php echo $sector_max; ?> sector universe to have:] --- Stage2
</div>
<?php if ($fedsecs > $sector_max): ?>
    <div class="alert alert-danger mb-4" role="alert">
        The number of Federation sectors must be smaller than the size of the universe!
    </div>
    <hr class="my-4">
<?php endif; ?>

<form action="create_universe.php" method="post" class="form-container">
    <input type="hidden" name="step" value="4">
    <input type="hidden" name="spp" value="<?php echo $spp; ?>">
    <input type="hidden" name="oep" value="<?php echo $oep; ?>">
    <input type="hidden" name="ogp" value="<?php echo $ogp; ?>">
    <input type="hidden" name="gop" value="<?php echo $gop; ?>">
    <input type="hidden" name="enp" value="<?php echo $enp; ?>">
    <input type="hidden" name="initscommod" value="<?php echo $initscommod; ?>">
    <input type="hidden" name="initbcommod" value="<?php echo $initbcommod; ?>">
    <input type="hidden" name="nump" value="<?php echo $nump; ?>">
    <input type="hidden" name="fedsecs" value="<?php echo $fedsecs; ?>">
    <input type="hidden" name="loops" value="<?php echo $loops; ?>">
    <input type="hidden" name="engage" value="3">
    <input type="hidden" name="swordfish" value="<?php echo $swordfish; ?>">

    <div class="mb-4">
        <h4 class="mb-3">Configuration Summary</h4>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Special ports</div>
            <div class="col-md-6"><?php echo $spp; ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Ore ports</div>
            <div class="col-md-6"><?php echo $oep; ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Organics ports</div>
            <div class="col-md-6"><?php echo $ogp; ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Goods ports</div>
            <div class="col-md-6"><?php echo $gop; ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 fw-bold">Energy ports</div>
            <div class="col-md-6"><?php echo $enp; ?></div>
        </div>

        <hr class="my-3">

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Initial commodities to sell</div>
            <div class="col-md-6"><?php echo $initscommod; ?>%</div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 fw-bold">Initial commodities to buy</div>
            <div class="col-md-6"><?php echo $initbcommod; ?>%</div>
        </div>

        <hr class="my-3">

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Empty sectors</div>
            <div class="col-md-6"><?php echo $empty; ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Federation sectors</div>
            <div class="col-md-6"><?php echo $fedsecs; ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-md-6 fw-bold">Loops</div>
            <div class="col-md-6"><?php echo $loops; ?></div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 fw-bold">Unowned planets</div>
            <div class="col-md-6"><?php echo $nump; ?></div>
        </div>
    </div>

    <div class="d-grid gap-2 d-md-flex justify-content-md-center mb-4">
        <input type="submit" class="btn btn-primary" value="Confirm">
    </div>
</form>
<?php include "footer.php"; ?>
