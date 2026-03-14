<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Game\Exception\GameMoveToSectorFightersProblemException;
use BNT\Game\Servant\GameCheckFightersServant;
use BNT\Game\Servant\GameCheckMinesServant;
use BNT\Game\Servant\GameSectorMinesServant;
use BNT\MovementLog\DAO\MovementLogDAO;

class GameMoveToSectorWhenIsNotAProblemServant extends \UUA\Servant
{

    use \BNT\Traits\PlayerinfoTrait;
    use \BNT\Traits\MessagesTrait;
    use \BNT\Traits\TranslateTrait;

    public int $sector;
    public int $turns;
    public int $energyScooped;

    #[\Override]
    public function serve(): void
    {
        $checkFighters = GameCheckFightersServant::new($this->container);
        $checkFighters->playerinfo = $this->playerinfo;
        $checkFighters->sector = $this->sector;
        $checkFighters->serve();

        if (!$checkFighters->ok) {
            throw new GameMoveToSectorFightersProblemException;
        }

        MovementLogDAO::call($this->container, $this->playerinfo['ship_id'], $this->sector);
        
        $this->playerinfo['cleared_defences'] = '';
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

        if (!$sectorMines->shipDestroyed) {
            $this->messages[] = $this->t('l_move_complete');
        }
    }
}
