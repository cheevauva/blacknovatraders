<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipByIdDAO;

class ShipController extends BaseController
{

    public int $ship_id;
    public ?array $othership = null;

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->title = $this->l->ship_title;
        $this->ship_id = (int) $this->fromQueryParams('ship_id', 'ship_id ' . $this->l->is_required);
        $this->othership = ShipByIdDAO::call($this->container, $this->ship_id)->ship;

        $this->render('tpls/ship.tpl.php');
    }
}
