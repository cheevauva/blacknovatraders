<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipsByUserIdDAO;
use BNT\Ship\Servant\ShipChooseServant;

class ShipsController extends BaseController
{

    public array $ships;

    #[\Override]
    protected function init(): void
    {
        parent::init();

        $this->enableCheckShip = false;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        global $l;
        
        $this->title = $l->ships;
        $this->ships = ShipsByUserIdDAO::call($this->container, $this->userinfo['id'])->ships;
        $this->render('tpls/ships.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        $shipId = intval($this->parsedBody['ship_id'] ?? 0);

        if (empty($shipId)) {
            $this->redirectTo('ships.php');
            return;
        }

        $choose = ShipChooseServant::new($this->container);
        $choose->user = $this->userinfo;
        $choose->shipId = $shipId;
        $choose->serve();

        $this->redirectTo('main.php');
    }
}
