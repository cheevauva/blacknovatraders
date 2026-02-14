<?php

use BNT\Config\DAO\ConfigUpdateDAO;
use BNT\Game\Servant\GameCalculateStartParamsServant;
use BNT\EntryPoint\Servant\EntryPointCreateUniverseStep2Servant;

$disableAutoLogin = true;
$disableRegisterGlobalFix = true;

include 'config.php';

$title = "Create Universe";

$step = fromPOST('step');
$swordfish = fromPOST('swordfish');

if ($adminpass != $swordfish) {
    $step = 0;
}

$sector_max = round(fromPOST('sector_max', $sector_max));
$initscommod = fromPOST('initscommod', 100);
$initbcommod = fromPOST('initbcommod', 100);
$universe_size = fromPOST('universe_size', $universe_size);
$special = fromPOST('special', 1);
$ore = fromPOST('ore', 15);
$organics = fromPOST('organics', 10);
$goods = fromPOST('goods', 15);
$energy = fromPOST('energy', 10);
$planets = fromPOST('planets', 10);
$fedsecs = fromPOST('fedsecs', intval($sector_max / 200));

$startParams = GameCalculateStartParamsServant::new($container);
$startParams->special = $special;
$startParams->ore = $ore;
$startParams->organics = $organics;
$startParams->goods = $goods;
$startParams->energy = $energy;
$startParams->fedsecs = $fedsecs;
$startParams->planets = $planets;
$startParams->sectorMax = $sector_max;
$startParams->buyCommod = (float) $initscommod;
$startParams->sellCommod = (float) $initbcommod;
$startParams->serve();

$initBuyGoods = $startParams->initBuyGoods;
$initBuyOrganics = $startParams->initBuyOrganics;
$initBuyOre = $startParams->initBuyOre;
$initscommod = $startParams->initBuyEnergy;
$fedSectorsCount = $startParams->fedSectorsCount;
$specialSectorsCount = $startParams->specialSectorsCount;
$oreSectorsCount = $startParams->oreSectorsCount;
$organicsSectorsCount = $startParams->organicsSectorsCount;
$goodsSectorsCount = $startParams->goodsSectorsCount;
$energySectorsCount = $startParams->energySectorsCount;
$unownedPlanetsCount = $startParams->unownedPlanetsCount;

switch ($step) {
    case 1:
        include 'tpls/create_universe/create_universe_step1.tpl.php';
        return;
    case 2:
        $configUpdate = ConfigUpdateDAO::new($container);
        $configUpdate->config = [
            'sector_max' => $sector_max,
            'universe_size' => $universe_size,
            'organics_limit' => $organics_limit,
            'goods_limit' => $goods_limit,
            'ore_limit' => $ore_limit,
            'energy_limit' => $energy_limit,
        ];
        $configUpdate->serve();
        include 'tpls/create_universe/create_universe_step2.tpl.php';
        return;
    case 3:
        $entryPointCreateUniverseStep2 = EntryPointCreateUniverseStep2Servant::new($container);
        $entryPointCreateUniverseStep2->startParams = $startParams;
        $entryPointCreateUniverseStep2->sectorMax = (int) $sector_max;
        $entryPointCreateUniverseStep2->universeSize = (int) $universe_size;
        $entryPointCreateUniverseStep2->serve();
        include 'tpls/create_universe/create_universe_step3.tpl.php';
        break;
    default:
        include 'tpls/create_universe/create_universe_login.tpl.php';
        break;
}
