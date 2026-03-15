<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Translate;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\SectorDefence\DAO\SectorDefenceByIdDAO;
use BNT\SectorDefence\DAO\SectorDefencesCleanUpDAO;
use BNT\SectorDefence\DAO\SectorDefencesUpdateDAO;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Exception\WarningException;
use BNT\Game\Servant\GameMessageDefenceOwnerServant;
use BNT\Game\Servant\GameExplodeMinesServant;
use BNT\Game\Servant\GameSectorFightersServant;

class ModifyDefencesController extends BaseController
{

    public protected(set) int $defenceId;
    public protected(set) array $sectorinfo;
    public protected(set) array $defenceinfo;
    public protected(set) Translate|string $defenceOwner;
    public protected(set) Translate $defenceType;
    public protected(set) int $qty;
    public protected(set) string $response;
    public protected(set) array $fightersOwner;

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->t('l_md_title');
        $this->checkTurns();
        $this->defenceId = $this->fromQueryParams('defence_id')->notEmpty()->asInt();
        $this->sectorinfo = SectorByIdDAO::call($this->container, $this->playerinfo['sector'])->sector ?? throw new WarningException()->t(['l_sector', 'l_not_found']);
        $this->defenceinfo = SectorDefenceByIdDAO::call($this->container, $this->defenceId)->defence ?? throw new WarningException()->t('l_md_nothere');
        $this->defenceType = $this->defenceinfo['defence_type'] == 'F' ? $this->t('l_fighters') : $this->t('l_mines');
        $this->qty = $this->defenceinfo['quantity'];

        if ($this->defenceinfo['ship_id'] == $this->playerinfo['ship_id']) {
            $this->defenceOwner = $this->t('l_md_you');
        } else {
            $this->fightersOwner = ShipByIdDAO::call($this->container, $this->defenceinfo['ship_id'])->ship;
            $this->defenceOwner = $this->fightersOwner['ship_name'];
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/modify_defences.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        $this->response = $this->fromParsedBody('response')->enum(['retrieve', 'fight', 'change'])->asString();

        match ($this->response) {
            'retrieve' => $this->retrieve(),
            'fight' => $this->fight(),
            'change' => $this->change(),
        };
    }

    protected function fight(): void
    {
        if ($this->defenceinfo['ship_id'] == $this->playerinfo['ship_id']) {
            throw new WarningException('l_md_yours');
        }

        $sector = $this->playerinfo['sector'];

        if ($this->defenceinfo['defence_type'] == 'F') {
            $totalSectorFighters = array_sum(array_column(SectorDefencesByCriteriaDAO::call($this->container, [
                'sector_id' => $sector,
                'defence_type' => 'M',
            ])->defences, 'quantity'));

            $sectorFighters = GameSectorFightersServant::new($this->container);
            $sectorFighters->totalSectorFighters = (int) $totalSectorFighters;
            $sectorFighters->sector = $sector;
            $sectorFighters->playerinfo = $this->playerinfo;
            $sectorFighters->serve();
            
            $this->messagesAppend($sectorFighters->messages);
        } else {
            $totalSectorMines = array_sum(array_column(SectorDefencesByCriteriaDAO::call($this->container, [
                'sector_id' => $sector,
                'defence_type' => 'M',
            ])->defences, 'quantity'));

            $playerBeams = NUM_BEAMS($this->playerinfo['beams']);

            if ($playerBeams > $this->playerinfo['ship_energy']) {
                $playerBeams = $this->playerinfo['ship_energy'];
            }

            if ($playerBeams > $totalSectorMines) {
                $playerBeams = $totalSectorMines;
            }

            $this->playerinfo['ship_energy'] -= $playerBeams;
            $this->playerinfoUpdate();

            GameExplodeMinesServant::call($this->container, $sector, $playerBeams);
            GameMessageDefenceOwnerServant::call($this->container, $sector, $this->t('l_md_msgdownerb', [
                'sector' => $sector,
                'mines' => $playerBeams,
                'name' => $this->playerinfo['ship_name'],
            ]));

            $this->messagesAppend($this->t(['l_md_bmines', '[playerbeams]', 'l_mines'], [
                'playerbeams' => $playerBeams,
            ]));
        }

        $this->redirectTo('main');
    }

    protected function change(): void
    {
        $mode = $this->fromParsedBody('mode')->enum(['attack', 'toll'])->asString();

        if ($this->defenceinfo['ship_id'] != $this->playerinfo['ship_id']) {
            throw new WarningException('l_md_notyours');
        }

        $this->defenceinfo['fm_setting'] = $mode;

        SectorDefencesUpdateDAO::call($this->container, $this->defenceinfo, [
            'defence_id' => $this->defenceId,
        ]);

        $this->playerinfoTurn();
        $this->playerinfoUpdate();
        $this->messagesAppend($this->t(['l_md_mode'], [
            'mode' => $mode == 'attack' ? $this->t('l_md_attack') : $this->t('l_md_toll'),
        ]));
        $this->redirectTo('main');
    }

    protected function retrieve(): void
    {
        if ($this->defenceinfo['ship_id'] != $this->playerinfo['ship_id']) {
            throw new WarningException('l_md_notyours');
        }

        $quantity = $this->fromParsedBody('quantity')->notEmpty()->asInt();

        if ($quantity < 0) {
            $quantity = 0;
        }

        if ($quantity > $this->defenceinfo['quantity']) {
            $quantity = $this->defenceinfo['quantity'];
        }

        $torpedoMax = NUM_TORPEDOES($this->playerinfo['torp_launchers']) - $this->playerinfo['torps'];
        $fighterMax = NUM_FIGHTERS($this->playerinfo['computer']) - $this->playerinfo['ship_fighters'];

        if ($this->defenceinfo['defence_type'] == 'F' && $quantity > $fighterMax) {
            $quantity = $fighterMax;
        }
        if ($this->defenceinfo['defence_type'] == 'M' && $quantity > $torpedoMax) {
            $quantity = $torpedoMax;
        }

        if ($quantity > 0) {
            $this->defenceinfo['quantity'] -= $quantity;

            SectorDefencesUpdateDAO::call($this->container, $this->defenceinfo, [
                'defence_id' => $this->defenceId,
            ]);

            if ($this->defenceinfo['defence_type'] == 'M') {
                $this->playerinfo['torps'] += $quantity;
            } else {
                $this->playerinfo['ship_fighters'] += $quantity;
            }

            SectorDefencesCleanUpDAO::call($this->container);
        }

        $this->playerinfoTurn();
        $this->playerinfoUpdate();
        $this->messagesAppend($this->t(['l_md_retr', '[quantity]', '[defenceType]'], [
            'quantity' => $quantity,
            'defenceType' => $this->defenceType,
        ]));
        $this->redirectTo('main');
    }
}
