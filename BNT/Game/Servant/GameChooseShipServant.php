<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Ship\DAO\ShipUpdateDAO;
use BNT\Ship\Servant\ShipCheckNewbieServant;
use BNT\Ship\Servant\ShipRestoreFromEscapePodServant;
use BNT\Ship\Servant\ShipRestoreAsNewbieServant;

class GameChooseShipServant extends \UUA\Servant
{

    public array $ship;

    #[\Override]
    public function serve(): void
    {
        global $l_login_died;
        global $l_login_looser;
        global $l_login_newbie;
        global $l_login_newlife;
        global $l_login_you_have_die;
        global $newbie_nice;

        $ship = $this->ship;

        if ($ship['ship_destroyed'] == 'N') {
            return;
        }


        if ($ship['ship_destroyed'] == 'Y' && $ship['dev_escapepod'] == 'Y') {
            $escapepod = ShipRestoreFromEscapePodServant::new($this->container);
            $escapepod->ship = $ship;
            $escapepod->serve();

            $this->tokenToShip($escapepod->ship);

            throw new Exception($l_login_died);
        }

        if ($ship['ship_destroyed'] == 'Y' && $newbie_nice !== 'YES') {
            throw new Exception($l_login_you_have_die . ' ' . $l_login_looser);
        }

        if ($ship['ship_destroyed'] == 'Y' && $newbie_nice == 'YES') {
            $checkNewbie = ShipCheckNewbieServant::new($this->container);
            $checkNewbie->ship = $ship;
            $checkNewbie->serve();

            if (!$checkNewbie->isNewbie) {
                throw new Exception($l_login_you_have_die . ' ' . $l_login_looser);
            }

            $restore = ShipRestoreAsNewbieServant::new($this->container);
            $restore->ship = $ship;
            $restore->serve();

            $this->tokenToShip($escapepod->ship);

            throw new Exception($l_login_you_have_die . ' ' . $l_login_newbie . ' ' . $l_login_newlife);
        }
    }
}
