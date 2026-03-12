<?php $self = BNT\Controller\MoveController::as($self); ?>
<?= include_header(); ?>
<form action="<?= $calledfrom; ?>" method="post">
    <div class="alert alert-danger" role="alert">
        <?= $l->t('l_chf_therearetotalfightersindest', ['chf_total_sector_fighters' => $self->totalSectorFighters]); ?>
    </div>
    <?php if (($self->defences[0]['fm_setting'] ?? null) == "toll") : ?>
        <div class="alert alert-warning" role="alert">
            <?= $l->t('l_chf_creditsdemanded', ['chf_number_fighterstoll' => $self->totalSectorFighters]); ?>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <input class="btn-check" type="radio" name="response" id="radio-retreat" value="retreat" checked>
        <label class="btn btn-secondary" for="radio-retreat">
            <?= $l->l_chf_youcanretreat; ?>
        </label>
    </div>
    <?php if (($self->defences[0]['fm_setting'] ?? null) == "toll") : ?>
        <div class="mb-3">
            <input class="btn-check" type="radio" name="response" id="radio-pay" value="pay">
            <label class="btn btn-secondary" for="radio-pay">
                <?= $l->l_chf_inputpay; ?>
            </label>
        </div>
    <?php endif; ?>
    <div class="mb-3">
        <input class="btn-check" type="radio" name="response" id="radio-fight" value="fight">
        <label class="btn btn-secondary" for="radio-fight">
            <?= $l->l_chf_inputfight; ?>
        </label>
    </div>
    <div class="mb-3">
        <input class="btn-check" type="radio" name="response" id="radio-sneak" value="sneak">
        <label class="btn btn-secondary" for="radio-sneak">
            <?= $l->l_chf_inputcloak; ?>
        </label>
    </div>
    <input type=submit class="btn btn-primary" value="<?= $l->l_chf_go; ?>"><br><br>
    <input type=hidden name=sector value="<?= $sector; ?>">
    <input type=hidden name=engage value=1>
    <input type=hidden name=destination value="<?= $destination; ?>">
</FORM>
<?= include_footer(); ?>
