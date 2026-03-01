<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\WarningException;
use BNT\Exception\InfoException;
use BNT\MovementLog\DAO\MovementLogDAO;

class EmergencyWarpController extends BaseController
{

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->l->ewd_title;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        global $sector_max;

        $dest_sector = rand(0, $sector_max);

        if (empty($this->playerinfo['dev_emerwarp'])) {
            throw new WarningException($this->l->ewd_none);
        }

        $this->playerinfo['sector'] = $dest_sector;
        $this->playerinfo['dev_emerwarp'] -= 1;
        $this->playerinfoUpdate();

        MovementLogDAO::call($this->container, $this->playerinfo['ship_id'], $dest_sector);

        throw new InfoException(str_replace('[sector]', (string) $dest_sector, $this->l->ewd_used));
    }
}
