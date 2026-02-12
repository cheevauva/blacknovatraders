<?php

declare(strict_types=1);

namespace Tests\Unit\Game\Servant;

use BNT\Game\Servant\GameCalculateStartParamsServant;

class GameCalculateStartParamsServantTest extends \Tests\UnitTestCase
{

    public function testMain(): void
    {
        global $organics_limit;
        global $goods_limit;
        global $ore_limit;
        global $energy_limit;

        $organics_limit = 100_000_000;
        $goods_limit = 100_000_000;
        $ore_limit = 100_000_000;
        $energy_limit = 100_000_000;

        $calculateStartParams = GameCalculateStartParamsServant::new(self::$container);
        $calculateStartParams->sectorMax = 152;
        $calculateStartParams->buyCommod = 64.5;
        $calculateStartParams->sellCommod = 35.5;
        $calculateStartParams->special = 2;
        $calculateStartParams->ore = 5;
        $calculateStartParams->organics = 7;
        $calculateStartParams->goods = 4;
        $calculateStartParams->energy = 11;
        $calculateStartParams->fedsecs = 8;
        $calculateStartParams->planets = 25;
        $calculateStartParams->serve();

        self::assertEquals(3, $calculateStartParams->specialSectorsCount);
        self::assertEquals(8, $calculateStartParams->oreSectorsCount);
        self::assertEquals(11, $calculateStartParams->organicsSectorsCount);
        self::assertEquals(6, $calculateStartParams->goodsSectorsCount);
        self::assertEquals(17, $calculateStartParams->energySectorsCount);
        self::assertEquals(12, $calculateStartParams->fedSectorsCount);
        self::assertEquals(107, $calculateStartParams->emptySectorsCount);
        self::assertEquals(38, $calculateStartParams->unownedPlanetsCount);
        self::assertEquals(64_500_000, $calculateStartParams->initBuyEnergy);
        self::assertEquals(64_500_000, $calculateStartParams->initBuyGoods);
        self::assertEquals(64_500_000, $calculateStartParams->initBuyOre);
        self::assertEquals(64_500_000, $calculateStartParams->initBuyOrganics);
        self::assertEquals(35_500_000, $calculateStartParams->initSellEnergy);
        self::assertEquals(35_500_000, $calculateStartParams->initSellGoods);
        self::assertEquals(35_500_000, $calculateStartParams->initSellOre);
        self::assertEquals(35_500_000, $calculateStartParams->initSellOrganics);
    }
}
