<?php

declare(strict_types=1);

namespace BNT\Controller;

// @todo replace sql to dao
class GalaxyController extends BaseController
{

    public int $sectorMax;
    public array $sectors;
    public array $explored_map;

    #[\Override]
    protected function preProcess(): void
    {
        global $sector_max;

        $this->sectors = range(1, $sector_max);
        $this->sectorMax = $sector_max;
        $this->title = $this->l->map_title;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $explored_sectors = db()->fetchAll("SELECT DISTINCT movement_log.sector_id, universe.port_type 
                                   FROM movement_log, universe 
                                   WHERE ship_id = :ship_id 
                                   AND movement_log.sector_id = universe.sector_id 
                                   ORDER BY sector_id ASC", [
            'ship_id' => $this->playerinfo['ship_id']
        ]);

        $this->explored_map = array_column($explored_sectors, 'port_type', 'sector_id');

        $this->render('tpls/galaxy.tpl.php');
    }
}
