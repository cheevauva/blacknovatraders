<?php

declare(strict_types=1);

namespace BNT\Sector\Servant;

use BNT\ServantInterface;
use BNT\Ship\Ship;
use BNT\Enum\BalanceEnum;

class SectorPortSpecialOfferServant implements ServantInterface
{
    public Ship $ship;
    public $dev_genesis_number = 0;
    public $dev_beacon_number = 0;
    public $dev_emerwarp_number = 0;
    public $dev_warpedit_number = 0;
    public $dev_minedeflector_number = 0;
    public $escapepod_purchase = 0;
    public $fuelscoop_purchase = 0;
    public $lssd_purchase = 0;
    public $computer_upgrade = 0;
    public $hull_upgrade = 0;
    public $engine_upgrade = 0;
    public $power_upgrade = 0;
    public $sensors_upgrade = 0;
    public $beams_upgrade = 0;
    public $armor_upgrade = 0;
    public $cloak_upgrade = 0;
    public $torp_launchers_upgrade = 0;
    public $shields_upgrade = 0;
    public $fighter_number = 0;
    public $torpedo_number = 0;
    public $armor_number = 0;
    public $colonist_number = 0;
    //
    public $hull_upgrade_cost = 0;
    public $engine_upgrade_cost = 0;
    public $power_upgrade_cost = 0;
    public $computer_upgrade_cost = 0;
    public $sensors_upgrade_cost = 0;
    public $beams_upgrade_cost = 0;
    public $armor_upgrade_cost = 0;
    public $cloak_upgrade_cost = 0;
    public $torp_launchers_upgrade_cost = 0;
    public $shields_upgrade_cost = 0;
    public $fighter_cost = 0;
    public $torpedo_cost = 0;
    public $armor_cost = 0;
    public $colonist_cost = 0;
    public $dev_genesis_cost = 0;
    public $dev_beacon_cost = 0;
    public $dev_emerwarp_cost = 0;
    public $dev_warpedit_cost = 0;
    public $dev_minedeflector_cost;
    public $dev_escapepod_cost = 0;
    public $dev_fuelscoop_cost = 0;
    public $dev_lssd_cost = 0;
    public $total_cost = 0;

    public function serve(): void
    {
        $ship = $this->ship;

        $this->hull_upgrade_cost = $this->calculateCost($this->hull_upgrade, $ship->hull);
        $this->engine_upgrade_cost = $this->calculateCost($this->engine_upgrade, $ship->engines);
        $this->power_upgrade_cost = $this->calculateCost($this->power_upgrade, $ship->power);
        $this->computer_upgrade_cost = $this->calculateCost($this->computer_upgrade, $ship->computer);
        $this->sensors_upgrade_cost = $this->calculateCost($this->sensors_upgrade, $ship->sensors);
        $this->beams_upgrade_cost = $this->calculateCost($this->beams_upgrade, $ship->beams);
        $this->armor_upgrade_cost = $this->calculateCost($this->armor_upgrade, $ship->armor);
        $this->cloak_upgrade_cost = $this->calculateCost($this->cloak_upgrade, $ship->cloak);
        $this->torp_launchers_upgrade_cost = $this->calculateCost($this->torp_launchers_upgrade, $ship->torp_launchers);
        $this->shields_upgrade_cost = $this->calculateCost($this->shields_upgrade, $ship->shields);
        //
        $this->fighter_number = $this->normalizeVal($this->fighter_number, $ship->getFighterMax());
        $this->torpedo_number = $this->normalizeVal($this->torpedo_number, $ship->getTorpedoesMax());
        $this->armor_number = $this->normalizeVal($this->armor_number, $ship->getArmorMax());
        $this->colonist_number = $this->normalizeVal($this->colonist_number, $ship->getColonistMax());
        $this->dev_genesis_number = $this->normalizeVal($this->dev_genesis_number);
        $this->dev_beacon_number = $this->normalizeVal($this->dev_beacon_number);
        $this->dev_warpedit_number = $this->normalizeVal($this->dev_warpedit_number);
        $this->dev_minedeflector_number = $this->normalizeVal($this->dev_minedeflector_number);
        $this->dev_emerwarp_number = min($this->normalizeVal($this->dev_emerwarp_number), BalanceEnum::max_emerwarp->val() - $ship->dev_emerwarp);
        //
        $this->fighter_cost = $this->fighter_number * BalanceEnum::fighter_price->val();
        $this->torpedo_cost = $this->torpedo_number * BalanceEnum::torpedo_price->val();
        $this->armor_cost = $this->armor_number * BalanceEnum::armor_price->val();
        $this->colonist_cost = $this->colonist_number * BalanceEnum::colonist_price->val();
        $this->dev_genesis_cost = $this->dev_genesis_number * BalanceEnum::dev_genesis_price->val();
        $this->dev_beacon_cost = $this->dev_beacon_number * BalanceEnum::dev_beacon_price->val();
        $this->dev_emerwarp_cost = $this->dev_emerwarp_number * BalanceEnum::dev_emerwarp_price->val();
        $this->dev_warpedit_cost = $this->dev_warpedit_number * BalanceEnum::dev_warpedit_price->val();
        $this->dev_minedeflector_cost = $this->dev_minedeflector_number * BalanceEnum::dev_minedeflector_price->val();

        if ($this->escapepod_purchase && !$ship->dev_escapepod) {
            $this->dev_escapepod_cost = BalanceEnum::dev_escapepod_price->val();
        }
        if ($this->fuelscoop_purchase && !$ship->dev_fuelscoop) {
            $this->dev_fuelscoop_cost = BalanceEnum::dev_fuelscoop_price->val();
        }
        if ($this->lssd_purchase && !$ship->dev_lssd) {
            $this->dev_lssd_cost = BalanceEnum::dev_lssd_price->val();
        }

        $this->total_cost = $this->calculateTotal();
    }

    private function calculateTotal()
    {
        return array_sum([
            $this->hull_upgrade_cost,
            $this->engine_upgrade_cost,
            $this->power_upgrade_cost,
            $this->computer_upgrade_cost,
            $this->sensors_upgrade_cost,
            $this->beams_upgrade_cost,
            $this->armor_upgrade_cost,
            $this->cloak_upgrade_cost,
            $this->torp_launchers_upgrade_cost,
            $this->shields_upgrade_cost,
            $this->fighter_cost,
            $this->torpedo_cost,
            $this->armor_cost,
            $this->colonist_cost,
            $this->dev_genesis_cost,
            $this->dev_beacon_cost,
            $this->dev_emerwarp_cost,
            $this->dev_warpedit_cost,
            $this->dev_minedeflector_cost,
            $this->dev_escapepod_cost,
            $this->dev_fuelscoop_cost,
            $this->dev_lssd_cost,
        ]);
    }

    private function phpChangeDelta($desiredvalue, $currentvalue)
    {
        $deltaCost = 0;
        $delta = $desiredvalue - $currentvalue;

        while ($delta > 0) {
            $deltaCost = $deltaCost + mypw(2, $desiredvalue - $delta);
            $delta = $delta - 1;
        }

        return $deltaCost * BalanceEnum::upgrade_cost->val();
    }

    private function calculateCost($amount, $shipAmount)
    {
        if ($amount > $shipAmount) {
            return $this->phpChangeDelta($amount, $shipAmount);
        }

        return 0;
    }

    private function normalizeVal($value, $max = null)
    {
        if ($value < 0) {
            return 0;
        }
        if (!is_null($max) && $value > $max) {
            $value = $max;
        }

        return intval(round(abs(intval($value ?? 0))));
    }
}
