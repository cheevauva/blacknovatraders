<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use BNT\Ship\Entity\Ship;

class ShipRetrieveByEmailAndCharacterAndShipnameDAO extends ShipDAO
{
    public string $email;
    public string $character_name;
    public string $ship_name;
    public ?Ship $ship;

    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());
        $qb->orWhere('email = :email');
        $qb->orWhere('character_name = :character_name');
        $qb->orWhere('ship_name = :ship_name');
        $qb->setParameters([
            'email' => $this->email,
            'character_name' => $this->character_name,
            'ship_name' => $this->ship_name,
        ]);
        $qb->setMaxResults(1);
        $this->ship = $this->asShip($qb->fetchAssociative() ?: []);
    }

    public static function call(string $email, string $character, string $shipname): ?Ship
    {
        $self = new static;
        $self->email = $email;
        $self->character_name = $character;
        $self->ship_name = $shipname;
        $self->serve();

        return $self->ship;
    }
}
