<?php

declare(strict_types=1);

namespace BNT;

enum BalanceEnum
{

    case ore_price;
    case ore_delta;
    case ore_rate;
    case ore_prate;
    case ore_limit;
    case upgrade_cost;
    case fighter_price;
    case torpedo_price;
    case armor_price;
    case colonist_price;
    case dev_escapepod_price;
    case dev_fuelscoop_price;
    case dev_lssd_price;
    case max_emerwarp;
    case dev_genesis_price;
    case dev_beacon_price;
    case dev_emerwarp_price;
    case dev_warpedit_price;
    case dev_minedeflector_price;
    case goods_price;
    case goods_delta;
    case goods_limit;
    case energy_price;
    case energy_delta;
    case energy_limit;
    case organics_price;
    case organics_delta;
    case organics_limit;
    case inventory_factor;
    case start_armor;
    case start_credits;
    case start_energy;
    case start_fighters;
    case max_turns;
    case sector_max;
    case max_rank;
    case level_factor;

    public function val(): mixed
    {
        global $level_factor;
        global $ore_price;
        global $ore_delta;
        global $ore_rate;
        global $ore_prate;
        global $ore_limit;
        global $goods_price;
        global $goods_delta;
        global $goods_limit;
        global $energy_price;
        global $energy_delta;
        global $energy_limit;
        global $organics_price;
        global $organics_delta;
        global $organics_limit;
        global $upgrade_cost;
        global $fighter_price;
        global $torpedo_price;
        global $armor_price;
        global $colonist_price;
        global $dev_escapepod_price;
        global $dev_fuelscoop_price;
        global $dev_lssd_price;
        global $max_emerwarp;
        global $dev_genesis_price;
        global $dev_beacon_price;
        global $dev_emerwarp_price;
        global $dev_warpedit_price;
        global $dev_minedeflector_price;
        global $inventory_factor;
        global $start_armor;
        global $start_credits;
        global $start_energy;
        global $start_fighters;
        global $max_turns;
        global $sector_max;
        global $max_rank;

        return match ($this) {
            BalanceEnum::ore_price => $ore_price,
            BalanceEnum::ore_delta => $ore_delta,
            BalanceEnum::ore_limit => $ore_limit,
            BalanceEnum::ore_rate => $ore_rate,
            BalanceEnum::ore_prate => $ore_prate,
            BalanceEnum::upgrade_cost => $upgrade_cost,
            BalanceEnum::fighter_price => $fighter_price,
            BalanceEnum::torpedo_price => $torpedo_price,
            BalanceEnum::armor_price => $armor_price,
            BalanceEnum::colonist_price => $colonist_price,
            BalanceEnum::dev_escapepod_price => $dev_escapepod_price,
            BalanceEnum::dev_fuelscoop_price => $dev_fuelscoop_price,
            BalanceEnum::dev_lssd_price => $dev_lssd_price,
            BalanceEnum::max_emerwarp => $max_emerwarp,
            BalanceEnum::dev_genesis_price => $dev_genesis_price,
            BalanceEnum::dev_beacon_price => $dev_beacon_price,
            BalanceEnum::dev_emerwarp_price => $dev_emerwarp_price,
            BalanceEnum::dev_warpedit_price => $dev_warpedit_price,
            BalanceEnum::dev_minedeflector_price => $dev_minedeflector_price,
            BalanceEnum::goods_price => $goods_price,
            BalanceEnum::goods_delta => $goods_delta,
            BalanceEnum::goods_limit => $goods_limit,
            BalanceEnum::energy_price => $energy_price,
            BalanceEnum::energy_delta => $energy_delta,
            BalanceEnum::energy_limit => $energy_limit,
            BalanceEnum::organics_price => $organics_price,
            BalanceEnum::organics_delta => $organics_delta,
            BalanceEnum::organics_limit => $organics_limit,
            BalanceEnum::inventory_factor => $inventory_factor,
            BalanceEnum::start_armor => $start_armor,
            BalanceEnum::start_credits => $start_credits,
            BalanceEnum::start_energy => $start_energy,
            BalanceEnum::start_fighters => $start_fighters,
            BalanceEnum::max_turns => $max_turns,
            BalanceEnum::sector_max => $sector_max,
            BalanceEnum::max_rank => $max_rank,
            BalanceEnum::level_factor => $level_factor,
        };
    }

}
