<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\ErrorException;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Zone\DAO\ZoneUpdateDAO;

class AdminZoneController extends BaseController
{

    public string $operation;
    public array $zones = [];
    public array $zone = [];

    #[\Override]
    protected function preProcess(): void
    {
        $this->isAdmin() ?: throw new ErrorException('You not admin');
        $this->operation = (string) $this->fromQueryParams('operation');
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        global $l;

        if ($this->operation === 'list') {
            $this->zones = db()->fetchAllKeyValue('SELECT zone_id,zone_name FROM zones ORDER BY zone_name');
            $this->render('tpls/admin/zonelist.tpl.php');
            return;
        }

        if ($this->operation === 'edit') {
            $zone = (int) $this->fromQueryParams('zone', 'zone ' . $l->is_required);

            $this->zone = ZoneByIdDAO::call($this->container, $zone)->zone;
            $this->render('tpls/admin/zoneedit.tpl.php');
            return;
        }

        parent::processGetAsHtml();
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        global $l;

        if ($this->operation === 'save') {
            $zone = (int) $this->fromQueryParams('zone', 'zone ' . $l->is_required);

            ZoneUpdateDAO::call($this->container, [
                'zone_name' => (string) fromPOST('zone_name'),
                'allow_beacon' => !fromPOST('zone_beacon') ? 'N' : 'Y',
                'allow_attack' => !fromPOST('zone_attack') ? 'N' : 'Y',
                'allow_warpedit' => !fromPOST('zone_warpedit') ? 'N' : 'Y',
                'allow_planet' => !fromPOST('zone_planet') ? 'N' : 'Y',
                'max_hull' => (int) fromPOST('zone_hull', 0)
            ], $zone);
            $this->redirectTo('admin.php?module=zone&operation=list');
            return;
        }

        parent::processPostAsJson();
    }
}
