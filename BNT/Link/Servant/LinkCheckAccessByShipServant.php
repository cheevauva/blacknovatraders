<?php

declare(strict_types=1);

namespace BNT\Link\Servant;

use BNT\Exception\WarningException;
use BNT\Exception\ErrorException;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Zone\DAO\ZoneByIdDAO;

class LinkCheckAccessByShipServant extends \UUA\Servant
{

    public array $ship;

    #[\Override]
    public function serve(): void
    {
        global $l;

        if ($this->ship['dev_warpedit'] < 1) {
            throw new WarningException($l->warp_none);
        }

        $sectorinfo = SectorByIdDAO::call($this->container, $this->ship['sector'])->sector ?? throw new ErrorException('sector');
        $zoneinfo = ZoneByIdDAO::call($this->container, $sectorinfo['zone_id'])->zone ?? throw new ErrorException('zone');

        if ($zoneinfo['allow_warpedit'] == 'N') {
            throw new WarningException($l->warp_forbid);
        }

        if ($zoneinfo['allow_warpedit'] == 'L') {
            $zoneowner_info = $zoneinfo;
            $zoneteam = ShipByIdDAO::call($this->container, $zoneowner_info['owner'])->ship;

            if ($zoneowner_info['owner'] == $this->ship['ship_id']) {
                return;
            }

            if (($zoneteam['team'] != $this->ship['team']) || ($this->ship['team'] == 0)) {
                throw new WarningException($l->warp_forbid);
            }
        }
    }
}
