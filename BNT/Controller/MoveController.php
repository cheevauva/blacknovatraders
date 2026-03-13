<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\WarningException;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Link\DAO\LinksByStartDAO;
use BNT\MovementLog\DAO\MovementLogDAO;
use BNT\Game\Servant\GameCheckFightersServant;
use BNT\Game\Servant\GameCheckMinesServant;
use BNT\Game\Servant\GameSectorMinesServant;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\Game\Servant\GameMoveServant;
use BNT\Translate;

class MoveController extends BaseController
{

    public int $sector;
    public array $sectorinfo = [];
    public array $links = [];
    public array $messages = [];
    public int $totalSectorFighters = 0;
    public array $defences = [];

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->t('l_move_title');
        $this->sector = $this->fromQueryParams('sector')->asInt();
        $this->checkTurns();

        SectorByIdDAO::call($this->container, $this->sector)->sector ?? throw new ErrorException()->t('l_sector', $this->sector, 'l_not_found');
        // Put the sector information into the array "sectorinfo"
        $this->sectorinfo = SectorByIdDAO::call($this->container, $this->playerinfo['sector'])->sector ?: throw new WarningException(['l_sector', 'l_not_found']);
        // Retrive all the warp links out of the current sector
        $this->links = LinksByStartDAO::call($this->container, $this->playerinfo['sector'])->links;

        $availableWarpLink = false;

        // Loop through the available warp links to make sure it's a valid move
        foreach ($this->links as $link) {
            if ($link['link_dest'] == $this->sector && $link['link_start'] == $this->playerinfo['sector']) {
                $availableWarpLink = true;
                break;
            }
        }

        if (!$availableWarpLink) {
            $this->playerinfo['cleared_defences'] = '';
            $this->playerinfoUpdate();

            throw new WarningException('l_move_failed');
        }

        $this->defences = SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'F',
        ])->defences;

        $this->totalSectorFighters = array_sum(array_column($this->defences, 'quantity'));
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/move.tpl.php');
    }

    #[\Override]
    protected function processPostAsHtml(): void
    {
        $checkFighters = GameCheckFightersServant::new($this->container);
        $checkFighters->playerinfo = $this->playerinfo;
        $checkFighters->sector = $this->sector;
        $checkFighters->serve();

        if (!$checkFighters->ok) {
            $this->playerinfo['cleared_defences'] = route('move', [
                'sector' => $this->sector,
            ]);
            $this->playerinfoUpdate();
            $this->render('tpls/move.tpl.php');
            return;
        }

        MovementLogDAO::call($this->container, $this->playerinfo['ship_id'], $this->sector);

        $this->playerinfo['sector'] = $this->sector;
        $this->playerinfoTurn();
        $this->playerinfoUpdate();

        $checkMines = GameCheckMinesServant::new($this->container);
        $checkMines->sector = $this->sector;
        $checkMines->playerinfo = $this->playerinfo;
        $checkMines->serve();

        if ($checkMines->ok) {
            $this->redirectTo('main');
            return;
        }

        $sectorMines = GameSectorMinesServant::new($this->container);
        $sectorMines->sector = $this->sector;
        $sectorMines->playerinfo = $this->playerinfo;
        $sectorMines->totalSectorMines = $checkMines->totalSectorMines;
        $sectorMines->serve();

        $this->messages = $sectorMines->messages;

        if (!$sectorMines->shipDestroyed) {
            $this->messages[] = $this->t('l_move_complete');
        }

        $this->render('tpls/move_log.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        $checkFighters = GameCheckFightersServant::new($this->container);
        $checkFighters->playerinfo = $this->playerinfo;
        $checkFighters->sector = $this->sector;
        $checkFighters->serve();

        if (!$checkFighters->ok) {
            $move = GameMoveServant::new($this->container);
            $move->playerinfo = $this->playerinfo;
            $move->sector = $this->sector;
            $move->totalSectorFighters = $this->totalSectorFighters;
            $move->fightersOwner = $checkFighters->fightersOwner;
            $move->response = $this->fromParsedBody('response')->enum(['fight', 'retreat', 'pay', 'sneak'])->asString();
            $move->serve();

            $this->messages = $move->messages;
            
            if ($move->ok) {
                MovementLogDAO::call($this->container, $this->playerinfo['ship_id'], $this->sector);

                $this->playerinfo['sector'] = $this->sector;
                $this->playerinfoTurn();
                $this->playerinfoUpdate();
            }
        }

        if ($this->messages) {
            throw $this->messagesToException();
        }

        $checkMines = GameCheckMinesServant::new($this->container);
        $checkMines->sector = $this->sector;
        $checkMines->playerinfo = $this->playerinfo;
        $checkMines->serve();

        if (!$checkMines->ok) {
            $sectorMines = GameSectorMinesServant::new($this->container);
            $sectorMines->sector = $this->sector;
            $sectorMines->playerinfo = $this->playerinfo;
            $sectorMines->totalSectorMines = $checkMines->totalSectorMines;
            $sectorMines->serve();

            $this->messages = array_merge($this->messages, $sectorMines->messages);
        }

        if (empty($this->messages)) {
            $this->redirectTo('main');
        } else {
            throw $this->messagesToException();
        }
    }

    protected function messagesToException(): WarningException
    {
        $self = $this;

        return new WarningException()->t(array_map(function ($message) use ($self) {
            if ($message instanceof Translate) {
                $message->language($self->l);
            }

            return (string) $message;
        }, $this->messages));
    }
}
