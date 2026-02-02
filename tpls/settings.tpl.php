<?php $title = $l_settings_game;?>
<?php include("header.php"); ?>
<h1><?php echo $l_settings_game; ?></h1>
<table class="table table-hover">
    <tr><td><?php echo $l_settings_game_version; ?></td><td><?php echo $release_version; ?></td></tr>
    <tr><td><?php echo $l_settings_game_name; ?></td><td><?php echo $game_name; ?></td></tr>
    <tr><td><?php echo $l_settings_mine_hullsize; ?></td><td><?php echo $mine_hullsize; ?></td></tr>
    <tr><td><?php echo $l_settings_ewd_maxhullsize; ?></td><td><?php echo $ewd_maxhullsize; ?></td></tr>
    <tr><td><?php echo $l_settings_number_of_sectors; ?></td><td><?php echo NUMBER($sector_max); ?></td></tr>
    <tr><td><?php echo $l_settings_max_links_per_sector; ?></td><td><?php echo $link_max; ?></td></tr>
    <tr><td><?php echo $l_settings_fed_max_hull; ?></td><td><?php echo $fed_max_hull; ?></td></tr>
    <tr><td><?php echo $l_settings_allow_ibank; ?></td><td><?php echo $allow_ibank ? $l_settings_yes : $l_settings_no; ?></td></tr>
    <?php if ($allow_ibank): ?>
        <tr><td><?php echo $l_settings_igb_interest_rate; ?></td><td><?php echo $ibank_interest * 100; ?></td></tr>
        <tr><td><?php echo $l_settings_igb_loan_rate; ?></td><td><?php echo $ibank_loaninterest * 100; ?></td></tr>
    <?php endif; ?>
    <tr><td><?php echo $l_settings_tech_upgrade_bases; ?></td><td><?php echo $basedefense; ?></td></tr>
    <tr><td><?php echo $l_settings_colonists_limit; ?></td><td><?php echo NUMBER($colonist_limit) . "&nbsp;"; ?></td></tr>
    <tr><td><?php echo $l_settings_max_accumulated_turns; ?></td><td><?php echo NUMBER($max_turns); ?></td></tr>
    <tr><td><?php echo $l_settings_max_planets_per_sector; ?></td><td><?php echo $max_planets_sector; ?></td></tr>
    <tr><td><?php echo $l_settings_max_traderoutes_per_player; ?></td><td><?php echo $max_traderoutes_player; ?></td></tr>
    <tr><td><?php echo $l_settings_colonist_production_rate; ?></td><td><?php echo $colonist_production_rate; ?></td></tr>
    <tr><td><?php echo $l_settings_energy_per_fighter; ?></td><td><?php echo $energy_per_fighter; ?></td></tr>
    <tr><td><?php echo $l_settings_defence_degrade_rate; ?></td><td><?php echo $defence_degrade_rate * 100; ?></td></tr>
    <tr><td><?php echo $l_settings_min_bases_to_own; ?></td><td><?php echo $min_bases_to_own; ?></td></tr>
    <tr><td><?php echo $l_settings_planet_interest_rate; ?></td><td><?php echo NUMBER(($interest_rate - 1) * 100, 3); ?></td></tr>
    <tr><td><?php echo $l_settings_colonists_per_fighter; ?></td><td><?php echo NUMBER((1 / $colonist_production_rate) / $fighter_prate); ?></td></tr>
    <tr><td><?php echo $l_settings_colonists_per_torpedo; ?></td><td><?php echo NUMBER((1 / $colonist_production_rate) / $torpedo_prate); ?></td></tr>
    <tr><td><?php echo $l_settings_colonists_per_ore; ?></td><td><?php echo NUMBER((1 / $colonist_production_rate) / $ore_prate); ?></td></tr>
    <tr><td><?php echo $l_settings_colonists_per_organics; ?></td><td><?php echo NUMBER((1 / $colonist_production_rate) / $organics_prate); ?></td></tr>
    <tr><td><?php echo $l_settings_colonists_per_goods; ?></td><td><?php echo NUMBER((1 / $colonist_production_rate) / $goods_prate); ?></td></tr>
    <tr><td><?php echo $l_settings_colonists_per_energy; ?></td><td><?php echo NUMBER((1 / $colonist_production_rate) / $energy_prate); ?></td></tr>
    <tr><td><?php echo $l_settings_colonists_per_credits; ?></td><td><?php echo NUMBER((1 / $colonist_production_rate) / $credits_prate); ?></td></tr>
</table>
<h1><?php echo $l_settings_game_scheduler; ?></h1>
<table class="table table-hover">
    <tr><td><?php echo $l_settings_ticks_happen; ?></td><td><?php echo $sched_ticks; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?php echo $l_settings_turns_happen; ?></td><td><?php echo $sched_turns; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?php echo $l_settings_defenses_checked; ?></td><td><?php echo $sched_turns; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?php echo $l_settings_xenobes_play; ?></td><td><?php echo $sched_turns; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
    <?php if ($allow_ibank): ?>
        <tr><td><?php echo $l_settings_igb_interest_accumulated; ?></td><td><?php echo $sched_igb; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
    <?php endif; ?>
    <tr><td><?php echo $l_settings_news_generated; ?></td><td><?php echo $sched_news; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?php echo $l_settings_planets_production; ?></td><td><?php echo $sched_planets; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?php echo $l_settings_ports_regenerate; ?></td><td><?php echo $sched_ports; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?php echo $l_settings_ships_towed; ?></td><td><?php echo $sched_turns; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?php echo $l_settings_rankings_generated; ?></td><td><?php echo $sched_ranking; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?php echo $l_settings_sector_defences_degrade; ?></td><td><?php echo $sched_degrade; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
    <tr><td><?php echo $l_settings_planetary_apocalypse; ?></td><td><?php echo $sched_apocalypse; ?> <?php echo $l_settings_minutes; ?>&nbsp;</td></tr>
</table>
<?php include("footer.php"); ?>
