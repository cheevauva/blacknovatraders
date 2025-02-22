<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Servant;
use BNT\Ship\DAO\ShipRetrieveByEmailDAO;
use BNT\Ship\DAO\ShipSaveDAO;
use BNT\Ship\Entity\Ship;
use BNT\Ship\Exception\ShipException;
use BNT\Log\Entity\LogLogin;
use BNT\Log\Entity\LogBadLogin;
use BNT\Log\DAO\LogCreateDAO;

class ShipLoginServant extends Servant
{

    public string $ip;
    public string $email;
    public string $password;
    public ?Ship $ship = null;

    public function serve(): void
    {
        $this->ship = ShipRetrieveByEmailDAO::call($this->container, $this->email);

        if (empty($this->ship)) {
            throw ShipException::notFound();
        }

        $ship = $this->ship;

        if (!password_verify($this->password, $ship->password)) {
            $badLogin = new LogBadLogin;
            $badLogin->ship_id = $ship->ship_id;
            $badLogin->ip = $this->ip;

            LogCreateDAO::call($this->container, $badLogin);

            throw ShipException::incorrectPassword($ship);
        }

        if ($ship->ship_destroyed) {
            throw ShipException::hasBeenDestroyed($ship);
        }

        $ship->last_login = new \DateTime;
        $ship->ip_address = $this->ip;

        ShipSaveDAO::call($this->container, $ship);

        $login = new LogLogin;
        $login->ship_id = $ship->ship_id;
        $login->ip = $this->ip;

        LogCreateDAO::call($this->container, $login);
    }

}
