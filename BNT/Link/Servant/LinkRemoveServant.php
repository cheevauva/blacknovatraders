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
        $tgSector = SectorByIdDAO::call($this->container, $this->targetSector)->sector ?? throw new WarningException('l_warp_nosector');
        $tgZone = ZoneByIdDAO::call($this->container, $tgSector['zone_id'])->zone ?? throw new WarningException('l_warp_nozone');

        if ($tgZone['allow_warpedit'] == 'N' && $this->bothway) {
            throw new WarningException()->translate('l_warp_forbidtwo', [
                'target_sector' => $this->targetSector,
            ]);
        }

        if (!LinksByStartAndDestDAO::call($this->container, $this->sector, $this->targetSector)) {
            throw new WarningException()->translate('l_warp_unlinked', [
                'target_sector' => $this->targetSector,
            ]);
        }

        LinksDeleteByStartAndDestDAO::call($this->container, $this->sector, $this->targetSector);
    }
}
