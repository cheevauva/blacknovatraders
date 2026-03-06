<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Exception\WarningException;

class ShipController extends BaseController
{

    public int $ship_id;
    public ?array $othership = null;

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->title = $this->l->ship_title;
        $this->ship_id = $this->fromQueryParams('ship_id')->notEmpty()->asInt();
        $this->othership = ShipByIdDAO::call($this->container, $this->ship_id)->ship ?: throw new WarningException('l_not_found');

        $this->render('tpls/ship.tpl.php');
    }
}
