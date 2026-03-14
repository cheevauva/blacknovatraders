<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\ErrorException;
use BNT\Exception\WarningException;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Link\DAO\LinksByStartDAO;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\Game\Servant\GameMoveToSectorWhenIsAProblemServant;
use BNT\Game\Servant\GameMoveToSectorWhenIsNotAProblemServant;
use BNT\Game\Exception\GameMoveToSectorFightersProblemException;
use BNT\Translate;

class MoveController extends BaseController
{

    public protected(set) int $sector;
    public protected(set) array $sectorinfo = [];
    public protected(set) array $links = [];
    public protected(set) array $messages = [];
    public protected(set) int $totalSectorFighters = 0;
    public protected(set) array $defences = [];
    public protected(set) int $turns = 1;
    public protected(set) int $energyScooped = 0;
    public protected(set) bool $isRSMove = false;

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->t('l_move_title');
        $this->sector = $this->fromQueryParams('sector')->asInt();
        $this->checkTurns();

        SectorByIdDAO::call($this->container, $this->sector)->sector ?? throw new ErrorException()->t(['l_sector', strval($this->sector), 'l_not_found']);
        // Put the sector information into the array "sectorinfo"
        $this->sectorinfo = SectorByIdDAO::call($this->container, $this->playerinfo['sector'])->sector ?: throw new WarningException(['l_sector', 'l_not_found']);
        $this->defences = SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'F',
        ])->defences;

        $this->totalSectorFighters = array_sum(array_column($this->defences, 'quantity'));

        $this->checkAccess();
    }

    protected function checkAccess(): void
    {
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
        $this->render('tpls/move.tpl.php');
    }

    protected function clearedDefencesRoute(): string
    {
        return route('move', [
            'sector' => $this->sector,
        ]);
    }

    #[\Override]
    protected function processPostAsHtml(): void
    {
        try {
            
            $moveToSector = GameMoveToSectorWhenIsNotAProblemServant::new($this->container);
            $moveToSector->playerinfo = $this->playerinfo;
            $moveToSector->energyScooped = $this->energyScooped;
            $moveToSector->sector = $this->sector;
            $moveToSector->turns = $this->turns;
            $moveToSector->serve();

            if (!empty($this->messages)) {
                $this->messages = $moveToSector->messages;
                $this->render('tpls/move_log.tpl.php');
                return;
            }

            $this->redirectTo('main');
        } catch (GameMoveToSectorFightersProblemException $ex) {
            $this->playerinfo['cleared_defences'] = $this->clearedDefencesRoute();
            $this->playerinfoUpdate();
            $this->render('tpls/move.tpl.php');
        }
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        $moveToSector = GameMoveToSectorWhenIsAProblemServant::new($this->container);
        $moveToSector->playerinfo = $this->playerinfo;
        $moveToSector->sector = $this->sector;
        $moveToSector->turns = $this->turns;
        $moveToSector->isRSMove = $this->isRSMove;
        $moveToSector->energyScooped = $this->energyScooped;
        $moveToSector->solutionProblem = $this->fromParsedBody('response')->enum(['fight', 'retreat', 'pay', 'sneak'])->asString();
        $moveToSector->serve();

        if ($moveToSector->ok && empty($moveToSector->messages)) {
            $this->redirectTo('main');
            return;
        }

        throw $this->messagesToException($moveToSector->messages);
    }

    protected function messagesToException(array $messages): WarningException
    {
        $self = $this;

        return new WarningException()->t(array_map(function ($message) use ($self) {
            if ($message instanceof Translate) {
                $message->l($self->l);
            }

            return (string) $message;
        }, $messages));
    }
}
