<?php $self = BNT\Controller\SettingsController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<table class="table table-hover">
    <tr><td><?= $l->settings_game_version; ?></td><td><?= $GLOBALS['release_version']; ?></td></tr>
    <tr><td><?= $l->settings_game_name; ?></td><td><?= $GLOBALS['game_name']; ?></td></tr>
    <tr><td><?= $l->settings_mine_hullsize; ?></td><td><?= $GLOBALS['mine_hullsize']; ?></td></tr>
    <tr><td><?= $l->settings_ewd_maxhullsize; ?></td><td><?= $GLOBALS['ewd_maxhullsize']; ?></td></tr>
    <tr><td><?= $l->settings_number_of_sectors; ?></td><td><?= number($GLOBALS['sector_max']); ?></td></tr>
    <tr><td><?= $l->settings_max_links_per_sector; ?></td><td><?= $GLOBALS['link_max']; ?></td></tr>
    <tr><td><?= $l->settings_fed_max_hull; ?></td><td><?= $GLOBALS['fed_max_hull']; ?></td></tr>
    <tr><td><?= $l->settings_allow_ibank; ?></td><td><?= $GLOBALS['allow_ibank'] ? $l->settings_yes : $l->settings_no; ?></td></tr>
    <?php if ($GLOBALS['allow_ibank']) : ?>
        <tr><td><?= $l->settings_igb_interest_rate; ?></td><td><?= $GLOBALS['ibank_interest'] * 100; ?></td></tr>
        <tr><td><?= $l->settings_igb_loan_rate; ?></td><td><?= $GLOBALS['ibank_loaninterest'] * 100; ?></td></tr>
    <?php endif; ?>
    <tr><td><?= $l->settings_tech_upgrade_bases; ?></td><td><?= $GLOBALS['basedefense']; ?></td></tr>
    <tr><td><?= $l->settings_colonists_limit; ?></td><td><?= number($GLOBALS['colonist_limit']) . "&nbsp;"; ?></td></tr>
    <tr><td><?= $l->settings_max_accumulated_turns; ?></td><td><?= number($GLOBALS['max_turns']); ?></td></tr>
    <tr><td><?= $l->settings_max_planets_per_sector; ?></td><td><?= $GLOBALS['max_planets_sector']; ?></td></tr>
    <tr><td><?= $l->settings_max_traderoutes_per_player; ?></td><td><?= $GLOBALS['max_traderoutes_player']; ?></td></tr>
    <tr><td><?= $l->settings_colonist_production_rate; ?></td><td><?= $GLOBALS['colonist_production_rate']; ?></td></tr>
    <tr><td><?= $l->settings_energy_per_fighter; ?></td><td><?= $GLOBALS['energy_per_fighter']; ?></td></tr>
    <tr><td><?= $l->settings_defence_degrade_rate; ?></td><td><?= $GLOBALS['defence_degrade_rate'] * 100; ?></td></tr>
    <tr><td><?= $l->settings_min_bases_to_own; ?></td><td><?= $GLOBALS['min_bases_to_own']; ?></td></tr>
    <tr><td><?= $l->settings_planet_interest_rate; ?></td><td><?= number(($GLOBALS['interest_rate'] - 1) * 100, 3); ?></td></tr>
    <tr><td><?= $l->settings_colonists_per_fighter; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['fighter_prate']); ?></td></tr>
    <tr><td><?= $l->settings_colonists_per_torpedo; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['torpedo_prate']); ?></td></tr>
    <tr><td><?= $l->settings_colonists_per_ore; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['ore_prate']); ?></td></tr>
    <tr><td><?= $l->settings_colonists_per_organics; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['organics_prate']); ?></td></tr>
    <tr><td><?= $l->settings_colonists_per_goods; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['goods_prate']); ?></td></tr>
    <tr><td><?= $l->settings_colonists_per_energy; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['energy_prate']); ?></td></tr>
    <tr><td><?= $l->settings_colonists_per_credits; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['credits_prate']); ?></td></tr>
</table>
<h1><?= $l->settings_game_scheduler; ?></h1>
<table class="table table-hover">
    <tr><td><?= $l->settings_ticks_happen; ?></td><td><?= $GLOBALS['sched_ticks']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->settings_turns_happen; ?></td><td><?= $GLOBALS['sched_turns']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->settings_defenses_checked; ?></td><td><?= $GLOBALS['sched_turns']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->settings_xenobes_play; ?></td><td><?= $GLOBALS['sched_turns']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
    <?php if ($GLOBALS['allow_ibank']) : ?>
        <tr><td><?= $l->settings_igb_interest_accumulated; ?></td><td><?= $GLOBALS['sched_igb']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
    <?php endif; ?>
    <tr><td><?= $l->settings_news_generated; ?></td><td><?= $GLOBALS['sched_news']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->settings_planets_production; ?></td><td><?= $GLOBALS['sched_planets']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->settings_ports_regenerate; ?></td><td><?= $GLOBALS['sched_ports']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->settings_ships_towed; ?></td><td><?= $GLOBALS['sched_turns']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->settings_rankings_generated; ?></td><td><?= $GLOBALS['sched_ranking']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->settings_sector_defences_degrade; ?></td><td><?= $GLOBALS['sched_degrade']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->settings_planetary_apocalypse; ?></td><td><?= $GLOBALS['sched_apocalypse']; ?> <?= $l->settings_minutes; ?>&nbsp;</td></tr>
</table>
<?php include_footer(); ?>
