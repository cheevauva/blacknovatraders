<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\ErrorException;
use BNT\Zone\DAO\ZoneByIdDAO;
use BNT\Zone\DAO\ZoneUpdateDAO;
use BNT\Zone\DAO\ZonesByCriteriaDAO;

class AdminZoneController extends BaseController
{

    public string $operation;
    public array $zones = [];
    public array $zone = [];

    #[\Override]
    protected function preProcess(): void
    {
        $this->isAdmin() ?: throw new ErrorException('You not admin');
        $this->operation = $this->fromQueryParams('operation')->enum(['list', 'edit', 'save'])->asString();

        if (in_array($this->operation, ['edit', 'save'], true)) {
            $zoneId = $this->fromQueryParams('zone')->notEmpty()->asInt();
            $this->zone = ZoneByIdDAO::call($this->container, $zoneId)->zone ?? throw new ErrorException($this->l->not_found);
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        if ($this->operation === 'list') {
            $this->zones = array_column(ZonesByCriteriaDAO::call($this->container)->zones, 'zone_name', 'zone_id');
            $this->render('tpls/admin/zonelist.tpl.php');
            return;
        }

        if ($this->operation === 'edit') {
            $this->render('tpls/admin/zoneedit.tpl.php');
            return;
        }
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        if ($this->operation === 'save') {
            ZoneUpdateDAO::call($this->container, [
                'zone_name' => $this->fromParsedBody('zone_name')->notEmpty()->asString(),
                'allow_beacon' => !$this->fromParsedBody('zone_beacon')->asBool() ? 'N' : 'Y',
                'allow_attack' => !$this->fromParsedBody('zone_attack')->asBool() ? 'N' : 'Y',
                'allow_warpedit' => !$this->fromParsedBody('zone_warpedit')->asBool() ? 'N' : 'Y',
                'allow_planet' => !$this->fromParsedBody('zone_planet')->asBool() ? 'N' : 'Y',
                'max_hull' => $this->fromParsedBody('zone_hull')->default(0)->asInt()
            ], $this->zone['zone_id']);

            $this->redirectTo('admin', [
                'module' => 'zone',
                'operation' => 'list',
            ]);
            return;
        }
    }
}
