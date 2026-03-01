<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Sector\DAO\SectorByIdDAO;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\SectorDefence\DAO\SectorDefenceCreateDAO;
use BNT\SectorDefence\DAO\SectorDefencesUpdateDAO;
use BNT\SectorDefence\DAO\SectorDefenceByIdDAO;
use BNT\Exception\WarningException;
use BNT\Exception\SuccessException;

class MinesController extends BaseController
{

    public array $sector = [];
    public array $defences = [];
    public array $zoneinfo = [];
    public int $total_sector_fighters = 0;
    public int $total_sector_mines = 0;
    public int $num_defences;
    public bool $owns_all = true;
    public int $fighter_id = 0;
    public int $mine_id = 0;
    public bool $set_attack = true;
    public bool $set_toll = false;

    #[\Override]
    protected function preProcess(): void
    {
        $this->checkTurns();
        $this->title = $this->l->mines_title;
        $this->sector = SectorByIdDAO::call($this->container, $this->playerinfo['sector'])->sector;
        $this->defences = SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->playerinfo['sector']
        ])->items;
        $this->zoneinfo = ZoneByIdDAO::call($this->container, $this->sector['zone_id'])->zone;

        foreach ($this->defences as $defence) {
            if ($defence['defence_type'] == 'F') {
                $this->total_sector_fighters += $defence['quantity'];
            } else {
                $this->total_sector_mines += $defence['quantity'];
            }

            if ($defence['ship_id'] != $this->playerinfo['ship_id']) {
                $this->owns_all = false;
            }

            if ($defence['ship_id'] == $this->playerinfo['ship_id'] && $defence['defence_type'] == 'F') {
                $this->fighter_id = $defence['defence_id'];
            }

            if ($defence['ship_id'] == $this->playerinfo['ship_id'] && $defence['defence_type'] == 'M') {
                $this->mine_id = $defence['defence_id'];
            }
        }

        $this->num_defences = count($this->defences);

        if ($this->zoneinfo['allow_defenses'] == 'N') {
            throw new WarningException($this->l->mines_nopermit);
        }

        if (!empty($this->num_defences) && !$this->owns_all) {
            $defence_owner = $this->defences[0]['ship_id'];
            $fighters_owner = ShipByIdDAO::call($this->container, $defence_owner)->ship;

            if ($fighters_owner['team'] != $this->playerinfo['team'] || empty($this->playerinfo['team'])) {
                throw new WarningException($this->l->mines_nodeploy);
            }
        }


        if ($this->zoneinfo['allow_defenses'] == 'L') {
            $zone_owner = $this->zoneinfo['owner'];
            $zoneowner_info = ShipByIdDAO::call($this->container, $zone_owner)->ship;

            if ($zone_owner != $this->playerinfo['ship_id'] && ($zoneowner_info['team'] != $this->playerinfo['team'] || empty($this->playerinfo['team']))) {
                throw new WarningException($this->l->mines_nopermit);
            }
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        foreach ($this->defences as $defence) {
            if ($defence['ship_id'] == $this->playerinfo['ship_id'] && $defence['defence_type'] == 'F') {
                $this->set_attack = $defence['fm_setting'] == 'attack';
                $this->set_toll = $defence['fm_setting'] != 'attack';
            }
        }

        $this->render('tpls/mines.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        $nummines = abs(intval($this->fromParsedBody('nummines')));
        $numfighters = abs(intval($this->fromParsedBody('numfighters')));
        $mode = $this->fromParsedBody('mode');

        $messages = [];

        if ($nummines > $this->playerinfo['torps']) {
            throw new WarningException($this->l->mines_notorps);
        }

        if ($numfighters > $this->playerinfo['ship_fighters']) {
            throw new WarningException($this->l->mines_nofighters);
        }

        $messages[] = str_replace("[mines]", (string) $nummines, $this->l->mines_dmines);
        $messages[] = strtr($this->l->mines_dfighter, [
            '[fighters]' => $numfighters,
            '[mode]' => $mode,
        ]);

        if ($numfighters > 0) {
            if (!empty($this->fighter_id)) {
                $defence = SectorDefenceByIdDAO::call($this->container, $this->fighter_id)->defence;
                $defence['quantity'] += $numfighters;
                $defence['fm_setting'] = $mode;
   
                SectorDefencesUpdateDAO::call($this->container, $defence, [
                    'defence_id' => $this->fighter_id
                ]);
            } else {
                SectorDefenceCreateDAO::call($this->container, [
                    'ship_id' => $this->playerinfo['ship_id'],
                    'sector_id' => $this->playerinfo['sector'],
                    'defence_type' => 'F',
                    'quantity' => $numfighters,
                    'fm_setting' => $mode
                ]);
            }
        }

        if ($nummines > 0) {
            if (!empty($this->mine_id)) {
                $defence = SectorDefenceByIdDAO::call($this->container, $this->mine_id)->defence;
                $defence['quantity'] += $nummines;
                $defence['fm_setting'] = $mode;

                SectorDefencesUpdateDAO::call($this->container, $defence, [
                    'defence_id' => $this->mine_id
                ]);
            } else {
                SectorDefenceCreateDAO::call($this->container, [
                    'ship_id' => $this->playerinfo['ship_id'],
                    'sector_id' => $this->playerinfo['sector'],
                    'defence_type' => 'M',
                    'quantity' => $nummines,
                    'fm_setting' => $mode
                ]);
            }
        }


        $this->playerinfo['turns'] -= 1;
        $this->playerinfo['turns_used'] += 1;
        $this->playerinfo['ship_fighters'] -= $numfighters;
        $this->playerinfo['torps'] -= $nummines;
        $this->playerinfoUpdate();

        throw new SuccessException(implode("; ", $messages));
    }
}
