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

        if ($this->sector == $this->targetSector) {
            throw new WarningException('l_warp_cantsame');
        }

        $tgSector = SectorByIdDAO::call($this->container, $this->targetSector)->sector ?? throw new ErrorException('l_warp_nosector');
        $tgZone = ZoneByIdDAO::call($this->container, $tgSector['zone_id'])->zone;

        if ($tgZone['allow_warpedit'] == 'N' && !$this->oneway) {
            throw new WarningException()->translate('l_warp_twoerror', [
                'target_sector' => $this->targetSector
            ]);
        }

        if (LinksCountByStartDAO::call($this->container, $this->sector)->count >= $link_max) {
            throw new WarningException('l_warp_sectex');
        }

        $linksThere = LinksByStartAndDestDAO::call($this->container, $this->sector, $this->targetSector)->links;
        $linksHere = LinksByStartAndDestDAO::call($this->container, $this->targetSector, $this->sector)->links;

        if (!empty($linksThere)) {
            throw new WarningException()->translate('l_warp_linked', [
                'target_sector' => $this->targetSector,
            ]);
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
