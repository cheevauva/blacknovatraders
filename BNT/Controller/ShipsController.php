<?php

;

declare(strict_types=1);

namespace BNT\Controller;

use BNT\User\DAO\UserUpdateDAO;

class ShipsController extends BaseController
{

    public array $ships;

    protected function init(): void
    {
        global $userinfo;

        parent::init();

        $this->ships = db()->fetchAllKeyValue('SELECT ship_id, ship_name FROM ships WHERE user_id = :user_id', [
            'user_id' => $userinfo['id'],
        ]);
    }

    #[\Override]
    protected function processGet(): void
    {
        global $playerinfo;

        $this->render('tpls/ships.tpl.php');
    }

    #[\Override]
    protected function processPost(): void
    {
        global $userinfo;

        $shipId = intval($this->parsedBody['ship_id'] ?? 0) ?: throw new \Exception('ship_id');

        if (!isset($this->ships[$shipId])) {
            throw new \Exception('broken ship_id');
        }

        UserUpdateDAO::call($this->container, [
            'ship_id' => $shipId,
        ], $userinfo['id']);

        $this->redirectTo('main.php?id=' . $shipId);
    }
}
