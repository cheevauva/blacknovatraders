<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Game\Servant\GameCheckFightersServant;
use BNT\Game\Servant\GameSectorFightersServant;
use BNT\Game\Servant\GameDistributeTollServant;
use BNT\Game\Servant\GameCheckMinesServant;
use BNT\Game\Servant\GameSectorMinesServant;
use BNT\MovementLog\DAO\MovementLogDAO;
use BNT\Log\DAO\LogPlayerDAO;
use BNT\Log\LogTypeConstants;
use BNT\SectorDefence\DAO\SectorDefencesCleanUpDAO;

class GameMoveToSectorWhenIsAProblemServant extends \UUA\Servant
{

    use \BNT\Traits\PlayerinfoTrait;
    use \BNT\Traits\MessagesTrait;
    use \BNT\Traits\TranslateTrait;
    use \BNT\Traits\OkTrait;

    public int $sector;
    public int $turns;
    public int $energyScooped = 0;
    public bool $isRSMove = false;
    public string $solutionProblem;

    #[\Override]
    public function serve(): void
    {
        $this->ok();

        $checkFighters = GameCheckFightersServant::new($this->container);
        $checkFighters->playerinfo = $this->playerinfo;
        $checkFighters->sector = $this->sector;
        $checkFighters->serve();

        if (!$checkFighters->ok) {
            $this->playerinfo['cleared_defences'] = '';
            $this->playerinfoUpdate();

            match ($this->solutionProblem) {
                'fight' => $this->fight($checkFighters->totalSectorFighters),
                'retreat' => $this->retreat(),
                'pay' => $this->pay($checkFighters->totalSectorFighters),
                'sneak' => $this->sneak($checkFighters->totalSectorFighters, $checkFighters->fightersOwner),
            };

            SectorDefencesCleanUpDAO::call($this->container);
        }

        if ($this->isNotOk()) {
            return;
        }

        $this->playerinfoReload();

        MovementLogDAO::call($this->container, $this->playerinfo['ship_id'], $this->sector);

        $this->playerinfo['sector'] = $this->sector;
        $this->playerinfo['ship_energy'] += $this->energyScooped;
        $this->playerinfoTurn($this->turns);
        $this->playerinfoUpdate();

        $checkMines = GameCheckMinesServant::new($this->container);
        $checkMines->sector = $this->sector;
        $checkMines->playerinfo = $this->playerinfo;
        $checkMines->serve();

        if ($checkMines->ok) {
            return;
        }

        $sectorMines = GameSectorMinesServant::new($this->container);
        $sectorMines->sector = $this->sector;
        $sectorMines->playerinfo = $this->playerinfo;
        $sectorMines->totalSectorMines = $checkMines->totalSectorMines;
        $sectorMines->serve();

        $this->messagesAppend($sectorMines->messages);
    }

    protected function fight(int $totalSectorFighters): void
    {
        $sectorFighters = GameSectorFightersServant::new($this->container);
        $sectorFighters->isRSMove = $this->isRSMove;
        $sectorFighters->sector = $this->sector;
        $sectorFighters->totalSectorFighters = $totalSectorFighters;
        $sectorFighters->energyScooped = $this->energyScooped;
        $sectorFighters->playerinfo = $this->playerinfo;
        $sectorFighters->serve();

        $this->messagesAppend($sectorFighters->messages);
        $this->passOk($sectorFighters->ok);
    }

    protected function retreat(): void
    {
        $this->playerinfoTurn(2);
        $this->playerinfoUpdate();
        $this->messagesAppend($this->t('l_chf_youretreatback'));
        $this->notOk();
    }

    protected function pay(int $totalSectorFighters): void
    {
        global $fighter_price;

        $this->ok();

        $fightersToll = intval($totalSectorFighters * $fighter_price * 0.6);

        if ($this->playerinfo['credits'] < $fightersToll) {
            $this->messagesAppend($this->t(['l_chf_notenoughcreditstoll', 'l_chf_movefailed']));
            $this->notOk();
        } else {
            $this->playerinfo['credits'] -= $fightersToll;
            $this->playerinfoUpdate();

            $distributeToll = GameDistributeTollServant::new($this->container);
            $distributeToll->sector = $this->sector;
            $distributeToll->toll = $fightersToll;
            $distributeToll->totalFighters = $totalSectorFighters;
            $distributeToll->serve();

            LogPlayerDAO::call($this->container, $this->playerinfo['ship_id'], LogTypeConstants::LOG_TOLL_PAID, [$fightersToll, $this->sector]);
        }
    }

    protected function sneak(int $totalSectorFighters, array $fightersOwner): void
    {
        if (rand(1, 100) <= sensorsCloakSuccess($fightersOwner['sensors'], $this->playerinfo['cloak'])) {
            $this->messagesAppend($this->t('l_chf_thefightersdetectyou'));
            $this->fight($totalSectorFighters);
        }
    }
}
