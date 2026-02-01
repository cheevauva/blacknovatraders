<?php 
if (fromGET('debug')) {
    $debug = true;
    $total_sector_fighters = 999;
    $fighterstoll = 111;
}
?>
<FORM ACTION="<?php echo $calledfrom; ?>" METHOD=POST>
    <div class="alert alert-danger" role="alert">
        <?php echo str_replace("[chf_total_sector_fighters]", $total_sector_fighters, $l_chf_therearetotalfightersindest); ?>
    </div>
    <?php if ($defences[0]['fm_setting'] == "toll" || $debug) : ?>
        <div class="alert alert-warning" role="alert">
            <?php echo str_replace("[chf_number_fighterstoll]", NUMBER($fighterstoll), $l_chf_creditsdemanded); ?>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <input class="btn-check" type="radio" name="response" id="radio-retreat" value="retreat" checked>
        <label class="btn btn-secondary" for="radio-retreat">
            <?php echo $l_chf_youcanretreat; ?>
        </label>
    </div>
    <?php if ($defences[0]['fm_setting'] == "toll" || $debug) : ?>
        <div class="mb-3">
            <input class="btn-check" type="radio" name="response" id="radio-pay" value="pay">
            <label class="btn btn-secondary" for="radio-pay">
                <?php echo $l_chf_inputpay; ?>
            </label>
        </div>
    <?php endif; ?>
    <div class="mb-3">
        <input class="btn-check" type="radio" name="response" id="radio-fight" value="fight">
        <label class="btn btn-secondary" for="radio-fight">
            <?php echo $l_chf_inputfight; ?>
        </label>
    </div>
    <div class="mb-3">
        <input class="btn-check" type="radio" name="response" id="radio-sneak" value="sneak">
        <label class="btn btn-secondary" for="radio-sneak">
            <?php echo $l_chf_inputcloak; ?>
        </label>
    </div>
    <input type=submit class="btn btn-primary" value="<?php echo $l_chf_go; ?>"><br><br>
    <input type=hidden name=sector value="<?php echo $sector; ?>">
    <input type=hidden name=engage value=1>
    <input type=hidden name=destination value="<?php echo $destination; ?>">
</FORM>
