<?php include "header.php"; ?>
<form action="create_universe.php" method="post" class="form-container">
    <div class="mb-4">
        <h4 class="mb-3">Create Universe [Base/Planet Setup]</h4>

        <div class="mb-3">
            <label class="form-label">Percent Special</label>
            <input type="text" class="form-control" name="special" size="10" maxlength="10" value="1">
        </div>

        <div class="mb-3">
            <label class="form-label">Percent Ore</label>
            <input type="text" class="form-control" name="ore" size="10" maxlength="10" value="15">
        </div>

        <div class="mb-3">
            <label class="form-label">Percent Organics</label>
            <input type="text" class="form-control" name="organics" size="10" maxlength="10" value="10">
        </div>

        <div class="mb-3">
            <label class="form-label">Percent Goods</label>
            <input type="text" class="form-control" name="goods" size="10" maxlength="10" value="15">
        </div>

        <div class="mb-3">
            <label class="form-label">Percent Energy</label>
            <input type="text" class="form-control" name="energy" size="10" maxlength="10" value="10">
        </div>

        <div class="alert alert-info mb-4">
            Percent Empty: Equal to 100 - total of above.
        </div>

        <div class="mb-3">
            <label class="form-label">Initial Commodities to Sell [% of max]</label>
            <input type="text" class="form-control" name="initscommod" size="10" maxlength="10" value="100.00">
        </div>

        <div class="mb-3">
            <label class="form-label">Initial Commodities to Buy [% of max]</label>
            <input type="text" class="form-control" name="initbcommod" size="10" maxlength="10" value="100.00">
        </div>
    </div>

    <hr class="my-4">

    <div class="mb-4">
        <h4 class="mb-3">Create Universe [Sector/Link Setup] — Stage 1</h4>

        <div class="mb-3">
            <label class="form-label">Number of sectors total (<b>overrides config.php</b>)</label>
            <input type="text" class="form-control" name="sektors" size="10" maxlength="10" value="<?php echo $sector_max; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Number of Federation sectors</label>
            <input type="text" class="form-control" name="fedsecs" size="10" maxlength="10" value="<?php echo $fedsecs; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Number of loops</label>
            <input type="text" class="form-control" name="loops" size="10" maxlength="10" value="<?php echo $loops; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Percent of sectors with unowned planets</label>
            <input type="text" class="form-control" name="planets" size="10" maxlength="10" value="10">
        </div>
    </div>

    <input type="hidden" name="engage" value="1">
    <input type="hidden" name="step" value="2">
    <input type="hidden" name="swordfish" value="<?php echo $swordfish; ?>">

    <div class="d-grid gap-2 d-md-flex justify-content-md-center mt-4">
        <input type="submit" class="btn btn-primary me-md-2" value="Submit">
        <input type="reset" class="btn btn-secondary" value="Reset">
    </div>
</form>
<?php include "footer.php"; ?>
