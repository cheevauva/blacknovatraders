<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Sector\DAO\SectorByIdDAO;
use BNT\Exception\WarningException;
use BNT\Exception\SuccessException;

class DumpController extends BaseController
{

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->checkTurns();
        $this->title = $this->t('l_dump_title');

        $sectorinfo = SectorByIdDAO::call($this->container, $this->playerinfo['sector'])->sector;

        if (empty($this->playerinfo['ship_colonists'])) {
            throw new WarningException('l_dump_nocol');
        }

        if ($sectorinfo['port_type'] !== 'special') {
            throw new WarningException('l_dump_nono');
        }

        $this->playerinfo['ship_colonists'] = 0;
        $this->playerinfoTurn();
        $this->playerinfoUpdate();

        throw new SuccessException('l_dump_dumped');
    }
}
