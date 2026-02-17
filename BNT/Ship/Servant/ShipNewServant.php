<?php

declare(strict_types=1);

namespace BNT\Ship\Servant;

use BNT\Ship\DAO\ShipCreateDAO;
use BNT\Zone\DAO\ZoneCreateDAO;
use BNT\IBankAccount\DAO\IBankAccountCreateDAO;

class ShipNewServant extends \UUA\Servant
{
    public int $userId;
    public string $shipname;
    public string $character;
 
    public array $ship;
    public ?int $start_turns;

    #[\Override]
    public function serve(): void
    {
        $ship = $this->newShip();
        $ship['ship_id'] = $shipId = ShipCreateDAO::call($this->container, $ship)->id;

        ZoneCreateDAO::call($this->container, [
            'zone_name' => 'WebMaster\'s Territory',
            'owner' => $shipId,
        ])->id;

        IBankAccountCreateDAO::call($this->container, [
            'ship_id' => $shipId,
        ]);
        
        $this->ship = $ship;
    }

    /**
     * @global string $language
     * @global int $start_armor
     * @global int $start_credits
     * @global int $start_energy
     * @global int $start_fighters
     * @global int $start_turns
     * @return array<string, mixed>
     */
    protected function newShip(): array
    {
        global $start_armor;
        global $start_credits;
        global $start_energy;
        global $start_fighters;
        global $start_turns;

        return [
            'ship_name' => $this->shipname,
            'ship_destroyed' => 'N',
            'character_name' => $this->character,
            'armor_pts' => $start_armor,
            'credits' => $start_credits,
            'ship_energy' => $start_energy,
            'ship_fighters' => $start_fighters,
            'turns' => $this->start_turns ?? $start_turns, //(int) $this->mturnsMax(),
            'on_planet' => 'N',
            'dev_warpedit' => 0,
            'dev_genesis' => 0,
            'dev_beacon' => 0,
            'dev_emerwarp' => 0,
            'dev_escapepod' => 'N',
            'dev_fuelscoop' => 'N',
            'dev_minedeflector' => 0,
            'trade_colonists' => 'Y',
            'trade_fighters' => 'N',
            'trade_torps' => 'N',
            'trade_energy' => 'Y',
            'cleared_defences' => null,
            'dev_lssd' => (int) 'N',
            'user_id' => $this->userId,
        ];
    }

    protected function mturnsMax()
    {
        global $max_turns;

        $mturns = db()->column("SELECT MAX(turns_used + turns) AS mturns FROM ships");

        if ($mturns > $max_turns) {
            $mturns = $max_turns;
        }

        return $mturns;
    }
}
