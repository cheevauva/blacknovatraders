<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\ServantInterface;
use BNT\Ship\DAO\ShipRetrieveByEmailDAO;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Ship\Ship;
use BNT\Ship\Exception\ShipException;

class ShipLoginServant implements ServantInterface
{

    public string $ip;
    public string $email;
    public string $password;
    public Ship $ship;

    public function serve(): void
    {
        $retrieveByEmail = new ShipRetrieveByEmailDAO;
        $retrieveByEmail->email = $this->email;
        $retrieveByEmail->serve();

        if (empty($retrieveByEmail->ship)) {
            throw ShipException::notFound();
        }

        $ship = $this->ship = $retrieveByEmail->ship;

        if ($ship->password !== $this->password) {
            playerlog($ship->ship_id, LOG_BADLOGIN, $this->ip);
            throw ShipException::incorrectPassword($ship);
        }

        if ($ship->ship_destroyed) {
            throw ShipException::hasBeenDestroyed($ship);
        }

        $ship->last_login = new \DateTime;
        $ship->ip_address = $this->ip;

        $save = new ShipSaveDAO;
        $save->ship = $ship;
        $save->serve();

        playerlog($ship->ship_id, LOG_LOGIN, $this->ip);
    }

}
