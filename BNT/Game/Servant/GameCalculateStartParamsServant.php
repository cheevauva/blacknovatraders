<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

class GameCalculateStartParamsServant extends \UUA\Servant
{

    public int $sectorMax;
    public int $special;
    public int $ore;
    public int $organics;
    public int $goods;
    public int $energy;
    public int $fedsecs;
    public int $planets;
    public float $sellCommod;
    public float $buyCommod;
    //
    public int $specialSectorsCount;
    public int $oreSectorsCount;
    public int $goodsSectorsCount;
    public int $energySectorsCount;
    public int $organicsSectorsCount;
    public int $fedSectorsCount;
    public int $emptySectorsCount;
    public int $unownedPlanetsCount;
    public int $initSellOre;
    public int $initSellOrganics;
    public int $initSellGoods;
    public int $initSellEnergy;
    public int $initBuyOre;
    public int $initBuyOrganics;
    public int $initBuyGoods;
    public int $initBuyEnergy;

    #[\Override]
    public function serve(): void
    {
        global $organics_limit;
        global $goods_limit;
        global $ore_limit;
        global $energy_limit;

        $this->initSellOre = intval($ore_limit * $this->sellCommod / 100.0);
        $this->initSellOrganics = intval($organics_limit * $this->sellCommod / 100.0);
        $this->initSellGoods = intval($goods_limit * $this->sellCommod / 100.0);
        $this->initSellEnergy = intval($energy_limit * $this->sellCommod / 100.0);
        $this->initBuyOre = intval($ore_limit * $this->buyCommod / 100.0);
        $this->initBuyOrganics = intval($organics_limit * $this->buyCommod / 100.0);
        $this->initBuyGoods = intval($goods_limit * $this->buyCommod / 100.0);
        $this->initBuyEnergy = intval($energy_limit * $this->buyCommod / 100.0);
        $this->specialSectorsCount = (int) round($this->sectorMax * $this->special / 100);
        $this->oreSectorsCount = (int) round($this->sectorMax * $this->ore / 100);
        $this->organicsSectorsCount = (int) round($this->sectorMax * $this->organics / 100);
        $this->goodsSectorsCount = (int) round($this->sectorMax * $this->goods / 100);
        $this->energySectorsCount = (int) round($this->sectorMax * $this->energy / 100);
        $this->fedSectorsCount = (int) round($this->sectorMax * $this->fedsecs / 100);
        $this->emptySectorsCount = $this->sectorMax - $this->specialSectorsCount - $this->oreSectorsCount - $this->organicsSectorsCount - $this->goodsSectorsCount - $this->energySectorsCount;
        $this->unownedPlanetsCount = (int) round($this->sectorMax * ($this->planets / 100));
    }
}
