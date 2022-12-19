<?php

declare(strict_types=1);

namespace BNT\Ship\Mapper;

use BNT\Ship\Ship;
use BNT\ServantInterface;

class ShipMapper implements ServantInterface
{

    public ?array $row = null;
    public ?Ship $ship = null;

    public function serve(): void
    {
        if (empty($this->ship) && !empty($this->row)) {
            $ship = $this->ship = new Ship;
            $ship->id = intval($this->row['ship_id']);
            $ship->ip = $this->row['ip_address'];
            $ship->language = $this->row['lang'] ?? null;
            $ship->password = $this->row['password'];
            $ship->isDestroyed = strtoupper($this->row['ship_destroyed']) === 'Y';
            $ship->hasEscapePod = strtoupper($this->row['dev_escapepod']) === 'Y';
            $ship->dateLastLogin = $this->row['last_login'] ? new \DateTime($this->row['last_login']) : $ship->dateLastLogin;
        } 
        
        if (!empty($this->ship) && empty($this->row)) {
            $ship = $this->ship;
            $row = [];
            $row['last_login'] = $ship->dateLastLogin->format('Y-m-d H:i:s');
            $row['ip_address'] = $ship->ip;
            
            $this->row = $row;

        }
    }

}
