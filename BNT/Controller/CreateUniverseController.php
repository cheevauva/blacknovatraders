<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Config\DAO\ConfigUpdateDAO;
use BNT\Game\Servant\GameCalculateStartParamsServant;
use BNT\Game\Servant\GameUniverseDeployServant;
use BNT\Scheduler\Servant\SchedulersDeployServant;
use BNT\User\Servant\UserWithShipNewServant;

class CreateUniverseController extends BaseController
{

    public const STEP_1 = 1;
    public const STEP_2 = 2;
    public const STEP_3 = 3;

    public int $sched_ticks;
    public int $sched_turns;
    public int $sched_igb;
    public int $sched_news;
    public int $sched_planets;
    public int $sched_ports;
    public int $sched_degrade;
    public int $sched_apocalypse;
    public int $sched_ranking;
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
    public string $admin_mail;
    public string $admin_pass;

    #[\Override]
    protected function init(): void
    {
        global $admin_mail;
        global $admin_pass;
        global $sector_max;
        global $universe_size;
        global $ore_limit;
        global $organics_limit;
        global $energy_limit;
        global $goods_limit;
        global $sched_ticks;
        global $sched_turns;
        global $sched_igb;
        global $sched_news;
        global $sched_planets;
        global $sched_ports;
        global $sched_degrade;
        global $sched_apocalypse;
        global $sched_ranking;

        parent::init();
        
        $this->enableCheckAuth = false;
        $this->admin_mail = $admin_mail;
        $this->admin_pass = $admin_pass;
        $this->sector_max = $sector_max;
        $this->universe_size = $universe_size;
        $this->fedsecs = intval($sector_max / 200);
        $this->organics_limit = $organics_limit;
        $this->goods_limit = $goods_limit;
        $this->energy_limit = $energy_limit;
        $this->ore_limit = $ore_limit;
        $this->startParams = null;
        $this->sched_ticks = $sched_ticks;
        $this->sched_turns = $sched_turns;
        $this->sched_igb = $sched_igb;
        $this->sched_news = $sched_news;
        $this->sched_planets = $sched_planets;
        $this->sched_ports = $sched_ports;
        $this->sched_degrade = $sched_degrade;
        $this->sched_apocalypse = $sched_apocalypse;
        $this->sched_ranking = $sched_ranking;

        $this->title = "Create Universe";
    }

    #[\Override]
    protected function processGet(): void
    {
        $this->render('tpls/create_universe/create_universe_login.tpl.php');
    }

    protected function prepareInput(): void
    {
        $this->step = intval($this->parsedBody['step'] ?? $this->step);
        $this->swordfish = $this->parsedBody['swordfish'] ?? null;
        $this->sector_max = intval($this->parsedBody['sector_max'] ?? $this->sector_max);
        $this->admin_mail = strval($this->parsedBody['admin_mail'] ?? $this->admin_mail);
        $this->admin_pass = strval($this->parsedBody['admin_pass'] ?? $this->admin_pass);
        $this->initscommod = floatval($this->parsedBody['initscommod'] ?? $this->initscommod);
        $this->initbcommod = floatval($this->parsedBody['initbcommod'] ?? $this->initbcommod);
        $this->universe_size = intval($this->parsedBody['universe_size'] ?? $this->universe_size);
        $this->special = intval($this->parsedBody['special'] ?? $this->special);
        $this->ore = intval($this->parsedBody['ore'] ?? $this->ore);
        $this->organics = intval($this->parsedBody['organics'] ?? $this->organics);
        $this->goods = intval($this->parsedBody['goods'] ?? $this->goods);
        $this->energy = intval($this->parsedBody['energy'] ?? $this->energy);
        $this->planets = intval($this->parsedBody['planets'] ?? $this->planets);
        $this->fedsecs = intval($this->parsedBody['fedsecs'] ?? $this->fedsecs);
        $this->organics_limit = intval($this->parsedBody['organics_limit'] ?? $this->organics_limit);
        $this->goods_limit = intval($this->parsedBody['goods_limit'] ?? $this->goods_limit);
        $this->energy_limit = intval($this->parsedBody['energy_limit'] ?? $this->energy_limit);
        $this->ore_limit = intval($this->parsedBody['ore_limit'] ?? $this->ore_limit);
        $this->sched_ticks = intval($this->parsedBody['sched_ticks'] ?? $this->sched_ticks);
        $this->sched_turns = intval($this->parsedBody['sched_turns'] ?? $this->sched_turns);
        $this->sched_igb = intval($this->parsedBody['sched_igb'] ?? $this->sched_igb);
        $this->sched_news = intval($this->parsedBody['sched_news'] ?? $this->sched_news);
        $this->sched_planets = intval($this->parsedBody['sched_planets'] ?? $this->sched_planets);
        $this->sched_ports = intval($this->parsedBody['sched_ports'] ?? $this->sched_ports);
        $this->sched_degrade = intval($this->parsedBody['sched_degrade'] ?? $this->sched_degrade);
        $this->sched_apocalypse = intval($this->parsedBody['sched_apocalypse'] ?? $this->sched_apocalypse);
        $this->sched_ranking = intval($this->parsedBody['sched_ranking'] ?? $this->sched_ranking);
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

        switch ($step) {
            case self::STEP_1:
                $this->render('tpls/create_universe/create_universe_step1.tpl.php');
                break;
            case self::STEP_2:
                $this->startParams = $this->calculateStartParams();

                $configUpdate = ConfigUpdateDAO::new($this->container);
                $configUpdate->config = [
                    'admin_mail' => $this->admin_mail,
                    'sector_max' => $this->sector_max,
                    'universe_size' => $this->universe_size,
                    'organics_limit' => $this->organics_limit,
                    'goods_limit' => $this->goods_limit,
                    'ore_limit' => $this->ore_limit,
                    'energy_limit' => $this->energy_limit,
                    'sched_ticks' => $this->sched_ticks,
                    'sched_turns' => $this->sched_turns,
                    'sched_igb' => $this->sched_igb,
                    'sched_news' => $this->sched_news,
                    'sched_planets' => $this->sched_planets,
                    'sched_ports' => $this->sched_ports,
                    'sched_degrade' => $this->sched_degrade,
                    'sched_apocalypse' => $this->sched_apocalypse,
                    'sched_ranking' => $this->sched_ranking,
                ];
                $configUpdate->serve();

                $this->render('tpls/create_universe/create_universe_step2.tpl.php');
                break;
            case self::STEP_3:
                $this->startParams = $this->calculateStartParams();

                $universeDeploy = GameUniverseDeployServant::new($this->container);
                $universeDeploy->startParams = $this->startParams;
                $universeDeploy->sectorMax = $this->sector_max;
                $universeDeploy->universeSize = $this->universe_size;
                $universeDeploy->serve();

                $schedulersDeploy = SchedulersDeployServant::new($this->container);
                $schedulersDeploy->sched_apocalypse = $this->sched_apocalypse;
                $schedulersDeploy->sched_degrade = $this->sched_degrade;
                $schedulersDeploy->sched_igb = $this->sched_igb;
                $schedulersDeploy->sched_news = $this->sched_news;
                $schedulersDeploy->sched_planets = $this->sched_planets;
                $schedulersDeploy->sched_ports = $this->sched_ports;
                $schedulersDeploy->sched_ranking = $this->sched_ranking;
                $schedulersDeploy->sched_turns = $this->sched_turns;
                $schedulersDeploy->serve();

                $newUserWithShip = UserWithShipNewServant::new($this->container);
                $newUserWithShip->email = $this->admin_mail;
                $newUserWithShip->password = $this->admin_pass;
                $newUserWithShip->role = 'admin';
                $newUserWithShip->character = 'WebMaster';
                $newUserWithShip->shipname = 'WebMaster';
                $newUserWithShip->serve();

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
