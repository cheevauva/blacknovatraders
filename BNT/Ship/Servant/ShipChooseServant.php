<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Ship\DAO\ShipByIdDAO;
use BNT\User\DAO\UserUpdateDAO;
use BNT\Exception\ErrorException;

class ShipChooseServant extends \UUA\Servant
{

    public array $user;
    public int $shipId;

    #[\Override]
    public function serve(): void
    {
        global $l;

        $ship = ShipByIdDAO::call($this->container, $this->shipId)->ship;

        if (empty($ship)) {
            throw new ErrorException($l->ships_unavailable_ship);
        }

        if ($ship['user_id'] !== $this->user['id']) {
            throw new ErrorException($l->ships_unavailable_ship);
        }

        if ($ship['ship_destroyed'] == 'Y') {
            $this->tryingRestoreDestroyedShip($ship);
        }

        UserUpdateDAO::call($this->container, [
            'ship_id' => $this->shipId,
        ], $this->user['id']);
    }

    protected function tryingRestoreDestroyedShip(array $ship): void
    {
        global $l;
        global $newbie_nice;

        if ($ship['ship_destroyed'] == 'Y' && $ship['dev_escapepod'] == 'Y') {
            $escapepod = ShipRestoreFromEscapePodServant::new($this->container);
            $escapepod->ship = $ship;
            $escapepod->serve();
            return;
        }

        if ($ship['ship_destroyed'] == 'Y' && $newbie_nice !== 'YES') {
            throw new ErrorException($l->login_you_have_die . ' ' . $l->login_looser);
        }

        if ($ship['ship_destroyed'] == 'Y' && $newbie_nice == 'YES') {
            $checkNewbie = ShipCheckNewbieServant::new($this->container);
            $checkNewbie->ship = $ship;
            $checkNewbie->serve();

            if (!$checkNewbie->isNewbie) {
                throw new ErrorException($l->login_you_have_die . ' ' . $l->login_looser);
            }

            $restore = ShipRestoreAsNewbieServant::new($this->container);
            $restore->ship = $ship;
            $restore->serve();
            return;
        }
    }
}
