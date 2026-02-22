<?php

declare(strict_types=1);

namespace BNT\Link\Servant;

use BNT\Link\DAO\LinkCreateDAO;
use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Link\DAO\LinksByStartAndDestDAO;
use BNT\Exception\WarningException;
use BNT\Exception\ErrorException;
use BNT\Link\DAO\LinksCountByStartDAO;

class LinkCreateServant extends \UUA\Servant
{

    public int $sector;
    public int $targetSector;
    public bool $oneway;

    #[\Override]
    public function serve(): void
    {
        global $link_max;
        global $l;

        if ($this->sector == $this->targetSector) {
            throw new WarningException($l->warp_cantsame);
        }

        $tgSector = SectorByIdDAO::call($this->container, $this->targetSector)->sector ?? throw new ErrorException($l->warp_nosector);
        $tgZone = ZoneByIdDAO::call($this->container, $tgSector['zone_id'])->zone;

        if ($tgZone['allow_warpedit'] == 'N' && !$this->oneway) {
            throw new WarningException(str_replace('[target_sector]', (string) $this->targetSector, $l->warp_twoerror));
        }

        if (LinksCountByStartDAO::call($this->container, $this->sector)->count >= $link_max) {
            throw new WarningException($l->warp_sectex);
        }

        $linksThere = LinksByStartAndDestDAO::call($this->container, $this->sector, $this->targetSector)->links;
        $linksHere = LinksByStartAndDestDAO::call($this->container, $this->targetSector, $this->sector)->links;

        if (!empty($linksThere)) {
            throw new WarningException(str_replace('[target_sector]', (string) $this->targetSector, $l->warp_linked));
        }

        LinkCreateDAO::call($this->container, [
            'link_start' => $this->sector,
            'link_dest' => $this->targetSector,
        ]);

        if (!$this->oneway && empty($linksHere)) {
            LinkCreateDAO::call($this->container, [
                'link_start' => $this->targetSector,
                'link_dest' => $this->sector,
            ]);
        }
    }
}
