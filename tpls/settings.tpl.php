<?php $self = BNT\Controller\SettingsController::as($self); ?>
<?php include_header(); ?>
<?php bigtitle(); ?>
<table class="table table-hover">
    <tr><td><?= $l->l_settings_game_version; ?></td><td><?= $GLOBALS['release_version']; ?></td></tr>
    <tr><td><?= $l->l_settings_game_name; ?></td><td><?= $GLOBALS['game_name']; ?></td></tr>
    <tr><td><?= $l->l_settings_mine_hullsize; ?></td><td><?= $GLOBALS['mine_hullsize']; ?></td></tr>
    <tr><td><?= $l->l_settings_ewd_maxhullsize; ?></td><td><?= $GLOBALS['ewd_maxhullsize']; ?></td></tr>
    <tr><td><?= $l->l_settings_number_of_sectors; ?></td><td><?= number($GLOBALS['sector_max']); ?></td></tr>
    <tr><td><?= $l->l_settings_max_links_per_sector; ?></td><td><?= $GLOBALS['link_max']; ?></td></tr>
    <tr><td><?= $l->l_settings_fed_max_hull; ?></td><td><?= $GLOBALS['fed_max_hull']; ?></td></tr>
    <tr><td><?= $l->l_settings_allow_ibank; ?></td><td><?= $GLOBALS['allow_ibank'] ? $l->l_settings_yes : $l->l_settings_no; ?></td></tr>
    <?php if ($GLOBALS['allow_ibank']) : ?>
        <tr><td><?= $l->l_settings_igb_interest_rate; ?></td><td><?= $GLOBALS['ibank_interest'] * 100; ?></td></tr>
        <tr><td><?= $l->l_settings_igb_loan_rate; ?></td><td><?= $GLOBALS['ibank_loaninterest'] * 100; ?></td></tr>
    <?php endif; ?>
    <tr><td><?= $l->l_settings_tech_upgrade_bases; ?></td><td><?= $GLOBALS['basedefense']; ?></td></tr>
    <tr><td><?= $l->l_settings_colonists_limit; ?></td><td><?= number($GLOBALS['colonist_limit']) . "&nbsp;"; ?></td></tr>
    <tr><td><?= $l->l_settings_max_accumulated_turns; ?></td><td><?= number($GLOBALS['max_turns']); ?></td></tr>
    <tr><td><?= $l->l_settings_max_planets_per_sector; ?></td><td><?= $GLOBALS['max_planets_sector']; ?></td></tr>
    <tr><td><?= $l->l_settings_max_traderoutes_per_player; ?></td><td><?= $GLOBALS['max_traderoutes_player']; ?></td></tr>
    <tr><td><?= $l->l_settings_colonist_production_rate; ?></td><td><?= $GLOBALS['colonist_production_rate']; ?></td></tr>
    <tr><td><?= $l->l_settings_energy_per_fighter; ?></td><td><?= $GLOBALS['energy_per_fighter']; ?></td></tr>
    <tr><td><?= $l->l_settings_defence_degrade_rate; ?></td><td><?= $GLOBALS['defence_degrade_rate'] * 100; ?></td></tr>
    <tr><td><?= $l->l_settings_min_bases_to_own; ?></td><td><?= $GLOBALS['min_bases_to_own']; ?></td></tr>
    <tr><td><?= $l->l_settings_planet_interest_rate; ?></td><td><?= number(($GLOBALS['interest_rate'] - 1) * 100, 3); ?></td></tr>
    <tr><td><?= $l->l_settings_colonists_per_fighter; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['fighter_prate']); ?></td></tr>
    <tr><td><?= $l->l_settings_colonists_per_torpedo; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['torpedo_prate']); ?></td></tr>
    <tr><td><?= $l->l_settings_colonists_per_ore; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['ore_prate']); ?></td></tr>
    <tr><td><?= $l->l_settings_colonists_per_organics; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['organics_prate']); ?></td></tr>
    <tr><td><?= $l->l_settings_colonists_per_goods; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['goods_prate']); ?></td></tr>
    <tr><td><?= $l->l_settings_colonists_per_energy; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['energy_prate']); ?></td></tr>
    <tr><td><?= $l->l_settings_colonists_per_credits; ?></td><td><?= number((1 / $GLOBALS['colonist_production_rate']) / $GLOBALS['credits_prate']); ?></td></tr>
    
    <tr><td>G-TURNS-START</td><td><?= number($GLOBALS['start_turns']); ?></td></tr>
    <tr><td>G-SCHED-TYPE</td><td><?= $GLOBALS['sched_type'] ?? ''; ?></td></tr>
    <tr><td>G-SIZE-UNIVERSE</td><td><?= number($GLOBALS['universe_size']); ?></td></tr>
    <tr><td>G-DOOMSDAY-VALUE</td><td><?= number($GLOBALS['doomsday_value']); ?></td></tr>
    <tr><td>G-MONEY-PLANET</td><td><?= number(round($GLOBALS['interest_rate'] - 1, 4), 4); ?></td></tr>
    <tr><td>G-PORT-LIMIT-ORE</td><td><?= number($GLOBALS['ore_limit']); ?></td></tr>
    <tr><td>G-PORT-RATE-ORE</td><td><?= number($GLOBALS['ore_rate']); ?></td></tr>
    <tr><td>G-PORT-DELTA-ORE</td><td><?= number($GLOBALS['ore_delta']); ?></td></tr>
    <tr><td>G-PORT-LIMIT-ORGANICS</td><td><?= number($GLOBALS['organics_limit']); ?></td></tr>
    <tr><td>G-PORT-RATE-ORGANICS</td><td><?= number($GLOBALS['organics_rate']); ?></td></tr>
    <tr><td>G-PORT-DELTA-ORGANICS</td><td><?= number($GLOBALS['organics_delta']); ?></td></tr>
    <tr><td>G-PORT-LIMIT-GOODS</td><td><?= number($GLOBALS['goods_limit']); ?></td></tr>
    <tr><td>G-PORT-RATE-GOODS</td><td><?= number($GLOBALS['goods_rate']); ?></td></tr>
    <tr><td>G-PORT-DELTA-GOODS</td><td><?= number($GLOBALS['goods_delta']); ?></td></tr>
    <tr><td>G-PORT-LIMIT-ENERGY</td><td><?= number($GLOBALS['energy_limit']); ?></td></tr>
    <tr><td>G-PORT-RATE-ENERGY</td><td><?= number($GLOBALS['energy_rate']); ?></td></tr>
    <tr><td>G-PORT-DELTA-ENERGY</td><td><?= number($GLOBALS['energy_delta']); ?></td></tr>
    <tr><td>G-SOFA</td><td><?= isset($GLOBALS['sofa_on']) && $GLOBALS['sofa_on'] === true ? "1" : "0"; ?></td></tr>
    <tr><td>G-KSM</td><td><?= isset($GLOBALS['ksm_allowed']) && $GLOBALS['ksm_allowed'] ? "1" : "0"; ?></td></tr>
    <tr><td>S-CLOSED</td><td><?= isset($GLOBALS['server_closed']) && $GLOBALS['server_closed'] ? "1" : "0"; ?></td></tr>
    <tr><td>S-CLOSED-ACCOUNTS</td><td><?= isset($GLOBALS['account_creation_closed']) && $GLOBALS['account_creation_closed'] ? "1" : "0"; ?></td></tr>
    <tr><td>ALLOW_FULLSCAN</td><td><?= isset($GLOBALS['allow_fullscan']) && $GLOBALS['allow_fullscan'] ? "1" : "0"; ?></td></tr>
    <tr><td>ALLOW_NAVCOMP</td><td><?= isset($GLOBALS['allow_navcomp']) && $GLOBALS['allow_navcomp'] ? "1" : "0"; ?></td></tr>
    <tr><td>ALLOW_GENESIS_DESTROY</td><td><?= isset($GLOBALS['allow_genesis_destroy']) && $GLOBALS['allow_genesis_destroy'] ? "1" : "0"; ?></td></tr>
    <tr><td>INVENTORY_FACTOR</td><td><?= number($GLOBALS['inventory_factor'], 2); ?></td></tr>
    <tr><td>UPGRADE_COST</td><td><?= number($GLOBALS['upgrade_cost']); ?></td></tr>
    <tr><td>UPGRADE_FACTOR</td><td><?= number($GLOBALS['upgrade_factor'], 2); ?></td></tr>
    <tr><td>LEVEL_FACTOR</td><td><?= number($GLOBALS['level_factor'], 2); ?></td></tr>
    <tr><td>DEV_GENESIS_PRICE</td><td><?= number($GLOBALS['dev_genesis_price']); ?></td></tr>
    <tr><td>DEV_BEACON_PRICE</td><td><?= number($GLOBALS['dev_beacon_price']); ?></td></tr>
    <tr><td>DEV_EMERWARP_PRICE</td><td><?= number($GLOBALS['dev_emerwarp_price']); ?></td></tr>
    <tr><td>DEV_WARPEDIT_PRICE</td><td><?= number($GLOBALS['dev_warpedit_price']); ?></td></tr>
    <tr><td>DEV_MINEDEFLECTOR_PRICE</td><td><?= number($GLOBALS['dev_minedeflector_price']); ?></td></tr>
    <tr><td>DEV_ESCAPEPOD_PRICE</td><td><?= number($GLOBALS['dev_escapepod_price']); ?></td></tr>
    <tr><td>DEV_FUELSCOOP_PRICE</td><td><?= number($GLOBALS['dev_fuelscoop_price']); ?></td></tr>
    <tr><td>DEV_LSSD_PRICE</td><td><?= number($GLOBALS['dev_lssd_price']); ?></td></tr>
    <tr><td>FIGHTER_PRICE</td><td><?= number($GLOBALS['fighter_price']); ?></td></tr>
    <tr><td>TORPEDO_PRICE</td><td><?= number($GLOBALS['torpedo_price']); ?></td></tr>
    <tr><td>ARMOUR_PRICE</td><td><?= number($GLOBALS['armour_price'] ?? 0); ?></td></tr>
    <tr><td>COLONIST_PRICE</td><td><?= number($GLOBALS['colonist_price']); ?></td></tr>
    <tr><td>COLONIST_REPRODUCTION_RATE</td><td><?= number($GLOBALS['colonist_reproduction_rate'], 3); ?></td></tr>
    <tr><td>ORGANICS_CONSUMPTION</td><td><?= number($GLOBALS['organics_consumption'], 3); ?></td></tr>
    <tr><td>STARVATION_DEATH_RATE</td><td><?= number($GLOBALS['starvation_death_rate'], 3); ?></td></tr>
    <tr><td>CORP_PLANET_TRANSFERS</td><td><?= isset($GLOBALS['corp_planet_transfers']) && $GLOBALS['corp_planet_transfers'] ? "1" : "0"; ?></td></tr>
    <tr><td>MAX_TEAM_MEMBERS</td><td><?= number($GLOBALS['max_team_members'] ?? 0); ?></td></tr>
    <tr><td>SERVERTIMEZONE</td><td><?= htmlspecialchars($GLOBALS['servertimezone'] ?? ''); ?></td></tr>
</table>
<h1><?= $l->l_settings_game_scheduler; ?></h1>
<table class="table table-hover">
    <tr><td><?= $l->l_settings_ticks_happen; ?></td><td><?= $GLOBALS['sched_ticks']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->l_settings_turns_happen; ?></td><td><?= $GLOBALS['sched_turns']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->l_settings_defenses_checked; ?></td><td><?= $GLOBALS['sched_turns']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->l_settings_xenobes_play; ?></td><td><?= $GLOBALS['sched_turns']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
    <?php if ($GLOBALS['allow_ibank']) : ?>
        <tr><td><?= $l->l_settings_igb_interest_accumulated; ?></td><td><?= $GLOBALS['sched_igb']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
    <?php endif; ?>
    <tr><td><?= $l->l_settings_news_generated; ?></td><td><?= $GLOBALS['sched_news']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->l_settings_planets_production; ?></td><td><?= $GLOBALS['sched_planets']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->l_settings_ports_regenerate; ?></td><td><?= $GLOBALS['sched_ports']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->l_settings_ships_towed; ?></td><td><?= $GLOBALS['sched_turns']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->l_settings_rankings_generated; ?></td><td><?= $GLOBALS['sched_ranking']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->l_settings_sector_defences_degrade; ?></td><td><?= $GLOBALS['sched_degrade']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?= $l->l_settings_planetary_apocalypse; ?></td><td><?= $GLOBALS['sched_apocalypse']; ?> <?= $l->l_settings_minutes; ?>&nbsp;</td></tr>
</table>
<?php include_footer(); ?>