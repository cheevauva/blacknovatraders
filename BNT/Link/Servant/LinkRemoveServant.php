<?php

declare(strict_types=1);

namespace BNT\Link\Servant;

use BNT\Exception\WarningException;
use BNT\Link\DAO\LinksByStartAndDestDAO;
use BNT\Link\DAO\LinksDeleteByStartAndDestDAO;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Sector\DAO\SectorByIdDAO;

class LinkRemoveServant extends \UUA\Servant
{

    public int $targetSector;
    public int $sector;
    public bool $bothway;

    #[\Override]
    public function serve(): void
    {
        global $l;

        $tgSector = SectorByIdDAO::call($this->container, $this->targetSector)->sector ?? throw new WarningException($l->warp_nosector);
        $tgZone = ZoneByIdDAO::call($this->container, $tgSector['zone_id'])->zone ?? throw new WarningException($l->warp_nozone);

        if ($tgZone['allow_warpedit'] == 'N' && $this->bothway) {
            throw new WarningException(str_replace('[target_sector]', (string) $this->targetSector, $l->warp_forbidtwo));
        }

        if (!LinksByStartAndDestDAO::call($this->container, $this->sector, $this->targetSector)) {
            throw new WarningException(str_replace('[target_sector]', (string) $this->targetSector, $l->warp_unlinked));
        }

        LinksDeleteByStartAndDestDAO::call($this->container, $this->sector, $this->targetSector);
    }
}
