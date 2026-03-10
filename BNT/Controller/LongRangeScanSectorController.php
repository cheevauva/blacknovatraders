<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\WarningException;
use BNT\Link\DAO\LinksByStartDAO;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;
use BNT\Ship\DAO\ShipsByCriteriaDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Planet\DAO\PlanetsByCriteriaDAO;
use BNT\MovementLog\DAO\MovementLogLastShipInSectorDAO;

class LongRangeScanSectorController extends BaseController
{

    public array $sectorinfo;
    public int $sector;
    public int $fullscan_cost;
    public array $links = [];
    public array $ships = [];
    public array $planets = [];
    public int $fighters = 0;
    public int $mines = 0;
    public ?string $lastShipInSectorDetected = null;

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->t('l_lrs_title');
        $this->sector = $this->fromQueryParams('sector')->trim()->default(-1)->asInt();
        $this->checkTurns();
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        // user requested a single sector (standard) long range scan
        // get scanned sector information
        $this->sectorinfo = SectorByIdDAO::call($this->container, (int) $this->sector)->sector ?? throw new WarningException('l_not_found');
        $this->links = LinksByStartDAO::call($this->container, (int) $this->sector)->links;

        // get sectors which can be reached from the player's current sector
        $linksa = array_map('intval', array_column(LinksByStartDAO::call($this->container, $this->playerinfo['sector'])->links, 'link_dest'));

        if (!in_array(intval($this->sector), $linksa, true)) {
            throw new WarningException('l_lrs_cantscan');
        }

        if ($this->sector !== 0) {
            $ships = ShipsByCriteriaDAO::call($this->container, [
                'sector' => $this->sector,
                'on_planet' => 'N'
            ])->ships;

            $this->ships = [];

            foreach ($ships as $ship) {
                // display other ships in sector - unless they are successfully cloaked
                $success = SCAN_SUCCESS($this->playerinfo['sensors'], $ship['cloak']);
                if ($success < 5) {
                    $success = 5;
                }

                if ($success > 95) {
                    $success = 95;
                }

                $roll = rand(1, 100);
                if ($roll < $success) {

                    $this->ships[] = $ship['ship_name'];
                }
            }
        }

        $planets = PlanetsByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sectorinfo['sector_id'],
        ])->planets;

        $this->planets = [];

        foreach ($planets as $planet) {
            $ownerName = $this->t('l_unowned');
            $planetName = $this->t('l_unnamed');

            if (!empty($planet['owner'])) {
                $ownerName = ShipByIdDAO::call($this->container, $planet['owner'])->ship['ship_name'];
            }

            if (!empty($planet['name'])) {
                $planetName = $planet['name'];
            }

            $this->planets[] = sprintf('%s (%s)', $planetName, $ownerName);
        }


        $this->mines = array_sum(array_column(SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'M',
        ])->defences, 'quantity'));

        $this->fighters = array_sum(array_column(SectorDefencesByCriteriaDAO::call($this->container, [
            'sector_id' => $this->sector,
            'defence_type' => 'F',
        ])->defences, 'quantity'));

        $lastShipInSector = MovementLogLastShipInSectorDAO::new($this->container);
        $lastShipInSector->excludeShip = $this->playerinfo['ship_id'];
        $lastShipInSector->sector = $this->sector;
        $lastShipInSector->serve();

        if ($lastShipInSector->ship) {
            $this->lastShipInSectorDetected = ShipByIdDAO::call($this->container, $lastShipInSector->ship)->ship['ship_name'] ?? null;
        }

        $this->lastShipInSectorDetected ??= $this->t('l_none');

        if (empty($this->ships)) {
            $this->ships[] = $this->t('l_none');
        }

        if (empty($this->planets)) {
            $this->planets[] = $this->t('l_none');
        }
        $this->playerinfoTurn();
        $this->playerinfoUpdate();

        $this->render('tpls/lrscan_sector.tpl.php');
    }
}
