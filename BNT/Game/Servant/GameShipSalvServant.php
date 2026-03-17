<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Ship\Ship;

class GameShipSalvServant extends \UUA\Servant
{

    use \BNT\Traits\TranslateTrait;
    use \BNT\Traits\MessagesTrait;

    public Ship $player;
    public Ship $target;
    //
    public protected(set) float $salvGoods = 0;
    public protected(set) float $salvOre = 0;
    public protected(set) float $salvOrganics = 0;
    public protected(set) float $salvCredits = 0;

    #[\Override]
    public function serve(): void
    {
        $target = $this->target;
        $player = $this->player;

        $targetOre = round($target->ore / 2);
        $targetOrganics = round($target->organics / 2);
        $targetGoods = round($target->goods / 2);
        $playerHolds = NUM_HOLDS($player->hull) - array_sum([
            $player->ore,
            $player->organics,
            $player->goods,
            $player->colonists
        ]);
        $salvGoods = 0;
        $salvOre = 0;
        $salvOrganics = 0;

        if ($playerHolds > $targetGoods) {
            $salvGoods = $targetGoods;
            $playerHolds -= $targetGoods;
        } else {
            if ($playerHolds > 0) {
                $salvGoods = $playerHolds;
                $playerHolds = 0;
            }
        }

        if ($playerHolds > $targetOre) {
            $salvOre = $targetOre;
            $playerHolds -= $targetOre;
        } else {
            if ($playerHolds > 0) {
                $salvOre = $playerHolds;
                $playerHolds = 0;
            }
        }

        if ($playerHolds > $targetOrganics) {
            $salvOrganics = $targetOrganics;
            $playerHolds -= $targetOrganics;
        } else {
            if ($playerHolds > 0) {
                $salvOrganics = $playerHolds;
                $playerHolds = 0;
            }
        }

        $shipSalvageRate = rand(10, 20);
        $salvCredits = $target->upgradeValue() * $shipSalvageRate / 100;

        $this->messages[] = $this->t('l_att_salv', [
            'name' => $player->name,
            'salv_ore' => $salvOre,
            'salv_organics' => $salvOrganics,
            'salv_goods' => $salvGoods,
            'ship_salvage_rate' => $shipSalvageRate,
            'ship_salvage' => $salvCredits,
        ]);

        $this->salvGoods = $salvGoods;
        $this->salvOre = $salvOre;
        $this->salvOrganics = $salvOrganics;
        $this->salvCredits = $salvCredits;
    }
}
