<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\User\DAO\UserUpdateDAO;
use BNT\Ship\DAO\ShipsByUserIdDAO;
use BNT\Ship\DAO\ShipByIdDAO;
use BNT\Ship\Servant\ShipChooseServant;
use Exception;

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
    protected function processGet(): void
    {
        $this->loadShips();
        $this->render('tpls/ships.tpl.php');
    }

    protected function loadShips(): void
    {
        $this->ships = ShipsByUserIdDAO::call($this->container, $this->userinfo['id'])->ships;
    }

    #[\Override]
    protected function processPost(): void
    {
        $shipId = intval($this->parsedBody['ship_id'] ?? 0);

        if (empty($shipId)) {
            $this->redirectTo('ships.php');
            return;
        }

        try {
            $choose = ShipChooseServant::new($this->container);
            $choose->user = $this->userinfo;
            $choose->shipId = $shipId;
            $choose->serve();

            $this->redirectTo('main.php');
        } catch (Exception $ex) {
            $this->responseJsonByException($ex);
        }
    }
}
