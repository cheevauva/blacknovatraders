<?php

declare(strict_types=1);

namespace BNT\User\Servant;

use BNT\User\Servant\UserNewServant;
use BNT\Ship\Servant\ShipNewServant;
use BNT\User\DAO\UserUpdateDAO;

class UserWithShipNewServant extends \UUA\Servant
{

    public string $email;
    public string $password;
    public string $language = 'english';
    public string $role = 'user';
    public string $character;
    public string $shipname;
    //
    public array $ship;
    public array $user;

    #[\Override]
    public function serve(): void
    {
        $newUser = UserNewServant::new($this->container);
        $newUser->language = $this->language;
        $newUser->email = $this->email;
        $newUser->password = $this->password;
        $newUser->role = $this->role;
        $newUser->serve();

        $user = $newUser->user;

        $newShip = ShipNewServant::new($this->container);
        $newShip->character = $this->character;
        $newShip->shipname = $this->shipname;
        $newShip->userId = $user['id'];
        $newShip->serve();

        $ship = $newShip->ship;

        $user['ship_id'] = $ship['ship_id'];

        UserUpdateDAO::call($this->container, $user, $user['id']);

        $this->user = $user;
        $this->ship = $ship;
    }
}
