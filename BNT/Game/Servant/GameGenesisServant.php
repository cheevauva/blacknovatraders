<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Exception\WarningException;
use BNT\Exception\SuccessException;
use BNT\Planet\DAO\PlanetCreateDAO;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Ship\DAO\ShipByIdDAO;

class GameGenesisServant extends \UUA\Servant
{

    public array $ship;
    public int $numPlanets;

    #[\Override]
    public function serve(): void
    {
        global $max_planets_sector;

        global $l;

        if ($this->ship['on_planet'] === 'Y') {
            throw new WarningException($l->gns_onplanet);
        }

        $sector = SectorByIdDAO::call($this->container, $this->ship['sector'])->sector;

        $this->numPlanets = db()->column("SELECT COUNT(*) as count FROM planets WHERE sector_id= :sector", [
            'sector' => $this->ship['sector']
        ]);

        if ($this->numPlanets >= $max_planets_sector) {
            throw new WarningException($l->gns_full);
        }

        if ($this->ship['dev_genesis'] < 1) {
            throw new WarningException($l->gns_nogenesis);
        }

        $zone = ZoneByIdDAO::call($this->container, $sector['zone_id'])->zone;

        if ($zone['allow_planet'] === 'Y') {
            PlanetCreateDAO::call($this->container, $this->newPlanet());
            
            throw new SuccessException($l->gns_pcreate);
        }

        if ($zone['allow_planet'] == 'N') {
            throw new WarningException($l->gns_forbid);
        }

        if ($zone['corp_zone'] == 'Y' && ($this->ship['team'] != $zone['owner'])) {
            throw new WarningException($l->gns_bforbid);
        }

        if ($zone['corp_zone'] == 'N' && empty($this->ship['team'])) {
            if ($zone['owner'] != $this->ship['ship_id']) {
                throw new WarningException($l->gns_bforbid);
            }
        }

        if ($zone['corp_zone'] == 'N' && !empty($this->ship['team'])) {
            $owner = ShipByIdDAO::call($this->container, $zone['owner']);

            if ($owner['team'] != $this->ship['team']) {
                throw new WarningException($l->gns_bforbid);
            }
        }

        PlanetCreateDAO::call($this->container, $this->newPlanet());
        
        throw new SuccessException($l->gns_pcreate);
    }

    protected function planetName(): string
    {
        return implode('-', [
            substr($this->ship['ship_name'], 0, 1),
            $this->ship['sector'],
            $this->numPlanets + 1
        ]);
    }

    protected function newPlanet(): array
    {
        global $default_prod_ore;
        global $default_prod_organics;
        global $default_prod_goods;
        global $default_prod_energy;
        global $default_prod_fighters;
        global $default_prod_torp;

        return [
            'sector_id' => $this->ship['sector'],
            'name' => $this->planetName(),
            'owner' => $this->ship['ship_id'],
            'prod_ore' => $default_prod_ore,
            'prod_organics' => $default_prod_organics,
            'prod_goods' => $default_prod_goods,
            'prod_energy' => $default_prod_energy,
            'prod_fighters' => $default_prod_fighters,
            'prod_torp' => $default_prod_torp
        ];
    }
}
