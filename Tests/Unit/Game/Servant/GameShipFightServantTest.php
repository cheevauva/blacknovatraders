<?php

declare(strict_types=1);

namespace Tests\Unit\Game\Servant;

use BNT\Ship\Ship;
use BNT\Game\Servant\GameShipFightServant;
use BNT\Ship\Mapper\ShipRowToEntityMapper;

class GameShipFightServantTest extends \Tests\UnitTestCase
{

    protected function ship(int $id, string $name, int $fighters = 200): Ship
    {
        $ship = [
            'ship_id' => $id,
            'ship_name' => $name,
        ];
        $ship['hull'] = 0;
        $ship['engines'] = 0;
        $ship['power'] = 0;
        $ship['sensors'] = 0;
        $ship['computer'] = 0;
        $ship['beams'] = 3;
        $ship['torp_launchers'] = 100;
        $ship['torps'] = 50;
        $ship['armor'] = 0;
        $ship['armor_pts'] = 900;
        $ship['cloak'] = 0;
        $ship['shields'] = 1;
        $ship['sector'] = 0;
        $ship['ship_organics'] = 0;
        $ship['ship_ore'] = 0;
        $ship['ship_goods'] = 0;
        $ship['ship_energy'] = 1000;
        $ship['ship_colonists'] = 0;
        $ship['ship_fighters'] = $fighters;
        $ship['dev_warpedit'] = 0;
        $ship['dev_genesis'] = 0;
        $ship['dev_beacon'] = 0;
        $ship['dev_emerwarp'] = 0;
        $ship['dev_escapepod'] = 'Y';
        $ship['dev_fuelscoop'] = 'N';
        $ship['dev_minedeflector'] = 0;
        $ship['ship_destroyed'] = 'N';
        $ship['on_planet'] = 'N';
        $ship['cleared_defences'] = '';
        $ship['dev_lssd'] = 'N';
        $ship['turns'] = 100;
        $ship['turns_used'] = 0;

        return ShipRowToEntityMapper::call(self::$container, $ship)->ship;
    }

    public function testMain(): void
    {
        global $torp_dmg_rate;
        global $level_factor;

        $level_factor = 1.5;
        $torp_dmg_rate = 10;

        $player = $this->ship(1, 'PShip', 200);
        $target = $this->ship(2, 'TShip', 250);

        $fight = GameShipFightServant::new($this->container());
        $fight->player = $player;
        $fight->target = $target;
        $fight->serve();

        $messages = array_map(fn($m) => (string) $m, $fight->messages);

        self::assertCount(24, $messages);
        self::assertEquals([
            'PShip l_att_charge 338 l_att_beams',
            'PShip l_att_charge 150 l_att_shields',
            'TShip l_att_charge 338 l_att_beams',
            'TShip l_att_charge 150 l_att_shields',
            'PShip l_att_att TShip',
            'PShip Beams(lvl): 338(3) Shields(lvl): 150(1) Energy(Start): 512(1000) Torps(lvl): 50(100) TorpDmg: 500 Fighters(lvl): 200 Armor(lvl): 900 Does have Pod? 1',
            'TShip Beams(lvl): 338(3) Shields(lvl): 150(1) Energy(Start): 512(1000) Torps(lvl): 50(100) TorpDmg: 500 Fighters(lvl): 250 Armor(lvl): 900 Does have Pod? 1',
            'l_att_beams',
            'TShip l_att_lost 125 l_fighters',
            'PShip l_att_lost 100 l_fighters',
            'TShip l_att_shits 150 l_att_dmg',
            'PShip l_att_shits 150 l_att_dmg',
            'TShip l_att_ashit 63 l_att_dmg',
            'PShip l_att_ashit 88 l_att_dmg',
            'l_att_torps',
            'TShip l_att_lost 62 l_fighters',
            'PShip l_att_lost 50 l_fighters',
            'TShip l_att_ashit 438 l_att_dmg',
            'PShip l_att_ashit 450 l_att_dmg',
            'l_att_fighters',
            'TShip l_att_lost 50 l_fighters',
            'PShip l_att_lost 50 l_fighters',
            'TShip l_att_ashit 0 l_att_dmg',
            'PShip l_att_ashit 13 l_att_dmg',
        ], $messages);
    }
}
