<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Config\DAO\ConfigUpdateDAO;
use BNT\Game\Servant\GameCalculateStartParamsServant;
use BNT\Game\Servant\GameUniverseDeployServant;

class CreateUniverseController extends BaseController
{

    public int $sector_max;
    public int $universe_size;
    public float $initscommod = 100;
    public float $initbcommod = 100;
    public int $special = 1;
    public int $ore = 15;
    public int $organics = 10;
    public int $goods = 15;
    public int $energy = 10;
    public int $planets = 10;
    public int $fedsecs;
    public int $organics_limit;
    public int $goods_limit;
    public int $energy_limit;
    public int $ore_limit;
    public int $step = 0;
    public ?GameCalculateStartParamsServant $startParams;
    public ?string $swordfish = null;

    #[\Override]
    protected function init(): void
    {
        global $sector_max;
        global $universe_size;
        global $ore_limit;
        global $organics_limit;
        global $energy_limit;
        global $goods_limit;

        parent::init();

        $this->sector_max = $sector_max;
        $this->universe_size = $universe_size;
        $this->fedsecs = intval($sector_max / 200);
        $this->organics_limit = $organics_limit;
        $this->goods_limit = $goods_limit;
        $this->energy_limit = $energy_limit;
        $this->ore_limit = $ore_limit;
        $this->startParams = null;
    }

    #[\Override]
    protected function processGet(): void
    {
        $this->render('tpls/create_universe/create_universe_login.tpl.php');
    }

    protected function prepareInput(): void
    {
        $this->sector_max = intval($this->_POST['sector_max'] ?? $this->sector_max);
        $this->initscommod = floatval($this->_POST['initscommod'] ?? $this->initscommod);
        $this->initbcommod = floatval($this->_POST['initbcommod'] ?? $this->initbcommod);
        $this->universe_size = intval($this->_POST['universe_size'] ?? $this->universe_size);
        $this->special = intval($this->_POST['special'] ?? $this->special);
        $this->ore = intval($this->_POST['ore'] ?? $this->organics);
        $this->organics = intval($this->_POST['organics'] ?? $this->organics);
        $this->goods = intval($this->_POST['goods'] ?? $this->goods);
        $this->energy = intval($this->_POST['energy'] ?? $this->energy);
        $this->planets = intval($this->_POST['planets'] ?? $this->planets);
        $this->fedsecs = intval($this->_POST['fedsecs'] ?? $this->fedsecs);
        $this->swordfish = $this->_POST['swordfish'] ?? null;
        $this->organics_limit = intval($this->_POST['organics_limit'] ?? $this->organics_limit);
        $this->goods_limit = intval($this->_POST['goods_limit'] ?? $this->goods_limit);
        $this->energy_limit = intval($this->_POST['energy_limit'] ?? $this->energy_limit);
        $this->ore_limit = intval($this->_POST['ore_limit'] ?? $this->ore_limit);
        $this->step = intval($this->_POST['step'] ?? $this->step);
    }

    #[\Override]
    protected function processPost(): void
    {
        global $adminpass;

        $this->prepareInput();

        $step = $this->step;

        if ($adminpass != $this->swordfish) {
            $step = 0;
        }

        $this->startParams = $this->calculateStartParams();

        switch ($step) {
            case 1:
                $this->render('tpls/create_universe/create_universe_step1.tpl.php');
                break;
            case 2:
                $configUpdate = ConfigUpdateDAO::new($this->container);
                $configUpdate->config = [
                    'sector_max' => $this->sector_max,
                    'universe_size' => $this->universe_size,
                    'organics_limit' => $this->organics_limit,
                    'goods_limit' => $this->goods_limit,
                    'ore_limit' => $this->ore_limit,
                    'energy_limit' => $this->energy_limit,
                ];
                $configUpdate->serve();

                $this->render('tpls/create_universe/create_universe_step2.tpl.php');
                return;
            case 3:
                $entryPointCreateUniverseStep2 = GameUniverseDeployServant::new($this->container);
                $entryPointCreateUniverseStep2->startParams = $this->startParams;
                $entryPointCreateUniverseStep2->sectorMax = $this->sector_max;
                $entryPointCreateUniverseStep2->universeSize = $this->universe_size;
                $entryPointCreateUniverseStep2->serve();
                $this->render('tpls/create_universe/create_universe_step3.tpl.php');
                break;
            default:
                $this->render('tpls/create_universe/create_universe_login.tpl.php');
                break;
        }
    }

    protected function calculateStartParams(): GameCalculateStartParamsServant
    {
        $startParams = GameCalculateStartParamsServant::new($this->container);
        $startParams->special = $this->special;
        $startParams->ore = $this->ore;
        $startParams->organics = $this->organics;
        $startParams->goods = $this->goods;
        $startParams->energy = $this->energy;
        $startParams->fedsecs = $this->fedsecs;
        $startParams->planets = $this->planets;
        $startParams->sectorMax = $this->sector_max;
        $startParams->buyCommod = $this->initbcommod;
        $startParams->sellCommod = $this->initscommod;
        $startParams->serve();

        return $startParams;
    }
}
