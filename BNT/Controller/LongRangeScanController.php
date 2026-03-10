<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\WarningException;
use BNT\Game\DAO\GameLongRangeScanDAO;

class LongRangeScanController extends BaseController
{

    public int $fullscan_cost;
    public array $links = [];

    #[\Override]
    protected function preProcess(): void
    {
        global $allow_fullscan;
        global $fullscan_cost;

        $this->fullscan_cost = $fullscan_cost;
        $this->title = $this->t('l_lrs_title');

        if (!$allow_fullscan) {
            throw new WarningException('l_lrs_nofull');
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $linksBySector = GameLongRangeScanDAO::new($this->container);
        $linksBySector->sector = $this->playerinfo['sector'];
        $linksBySector->ship = $this->playerinfo['ship_id'];
        $linksBySector->serve();

        if ($this->playerinfo['turns'] < $this->fullscan_cost) {
            throw new WarningException()->t('l_lrs_noturns', [
                'fullscan_cost' => $this->fullscan_cost,
            ]);
        }

        $this->links = $linksBySector->links;

        $this->playerinfoTurn($this->fullscan_cost);
        $this->playerinfoUpdate();

        $this->render('tpls/lrscan.tpl.php');
    }
}
