<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\WarningException;
use BNT\Sector\Exception\SectorFightException;
use BNT\Sector\Exception\SectorChooseMoveException;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Link\DAO\LinksByStartDAO;
use BNT\MovementLog\DAO\MovementLogDAO;
use BNT\Game\Servant\GameCheckFightersServant;
use BNT\Game\Servant\GameSectorFightersServant;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
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
        $this->checkTurns();
        $this->sector = $this->fromQueryParams('sector')->asInt();
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
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->defences = SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'F',
        ])->defences;

        foreach ($this->defences as $defence) {
            $this->totalSectorFighters += $defence['quantity'];
        }
        
        try {
            $checkFighters = GameCheckFightersServant::new($this->container);
            $checkFighters->playerinfo = $this->playerinfo;
            $checkFighters->response = null;
            $checkFighters->sector = $this->sector;
            $checkFighters->calledFrom = route('move', [
                'sector' => $this->sector,
            ]);
            $checkFighters->serve();

            MovementLogDAO::call($this->container, $this->playerinfo['ship_id'], $this->sector);

            $this->playerinfo['sector'] = $this->sector;
            $this->playerinfoTurn();
            $this->playerinfoUpdate();

            ///include 'check_mines.php';
            $this->redirectTo('index');
        } catch (SectorChooseMoveException $ex) {
            $this->render('tpls/move_form.tpl.php');
        }
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        try {
            try {
                $checkFighters = GameCheckFightersServant::new($this->container);
                $checkFighters->playerinfo = $this->playerinfo;
                $checkFighters->response = null;
                $checkFighters->sector = $this->sector;
                $checkFighters->calledFrom = route('move', [
                    'sector' => $this->sector,
                ]);
                $checkFighters->serve();
            } catch (SectorFightException $ex) {
                $sectorFighters = GameSectorFightersServant::new($this->container);
                $sectorFighters->playerinfo = $this->playerinfo;
                $sectorFighters->serve();

                foreach ($sectorFighters->messages as $message) {
                    $message = Translate::as($message);
                    $message->language($this->l);
                }

                $this->messages = $sectorFighters->messages;
            }

            MovementLogDAO::call($this->container, $this->playerinfo['ship_id'], $this->sector);

            $this->playerinfo['sector'] = $this->sector;
            $this->playerinfoTurn();
            $this->playerinfoUpdate();

            ///include 'check_mines.php';
            $this->redirectTo('index');
        } catch (SectorChooseMoveException $ex) {
            $this->render('tpls/move_form.tpl.php');
        }
    }
}
