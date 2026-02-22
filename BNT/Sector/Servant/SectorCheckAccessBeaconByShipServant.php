<?php

declare(strict_types=1);

namespace BNT\Sector\Servant;

use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Exception\WarningException;

class SectorCheckAccessBeaconByShipServant extends \UUA\Servant
{

    public array $ship;

    #[\Override]
    public function serve(): void
    {
        global $l;

        if ($this->ship['dev_beacon'] < 1) {
            throw new WarningException($l->beacon_donthave);
        }

        $sectorinfo = SectorByIdDAO::call($this->container, $this->ship['sector'])->sector;
        $zoneinfo = ZoneByIdDAO::call($this->container, $sectorinfo['zone_id'])->zone;

        if ($zoneinfo['allow_beacon'] == 'N') {
            throw new WarningException($l->beacon_notpermitted);
        }

        if ($zoneinfo['allow_beacon'] == 'L') {
            $owner = ShipByIdDAO::call($this->container, $zoneinfo['owner'])->ship;

            if ($zoneinfo['owner'] == $this->ship['ship_id']) {
                return;
            }

            if (($owner['team'] != $this->ship['team']) || ($this->ship['team'] == 0)) {
                throw new WarningException($l->beacon_notpermitted);
            }
        }
    }
}
