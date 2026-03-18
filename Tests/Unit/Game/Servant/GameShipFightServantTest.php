<?php

declare(strict_types=1);

namespace Tests\Unit\Game\Servant;

use BNT\Ship\Ship;
use BNT\Game\Servant\GameShipFightServant;
use BNT\Translate;

class GameShipFightServantTest extends \Tests\UnitTestCase
{

    protected function ship(int $id, string $name): Ship
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
        $ship['beams'] = 0;
        $ship['torp_launchers'] = 100;
        $ship['torps'] = 50;
        $ship['armor'] = 0;
        $ship['armor_pts'] = 100;
        $ship['cloak'] = 0;
        $ship['shields'] = 0;
        $ship['sector'] = 0;
        $ship['ship_organics'] = 0;
        $ship['ship_ore'] = 0;
        $ship['ship_goods'] = 0;
        $ship['ship_energy'] = 1000;
        $ship['ship_colonists'] = 0;
        $ship['ship_fighters'] = 100;
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

        return new Ship($ship);
    }

    public function testMain(): void
    {
        global $torp_dmg_rate;
        global $level_factor;
        
        $level_factor = 1.5;
        $torp_dmg_rate = 10;

        $player = $this->ship(1, 'PShip');
        $target = $this->ship(2, 'TShip');

        $fight = GameShipFightServant::new($this->container());
        $fight->player = $player;
        $fight->target = $target;
        $fight->serve();

        $messages = array_map(fn(Translate $m) => (string) $m->l(self::$l), $fight->messages);
        
        self::assertEquals('PShip Beams(lvl): 100(0) Shields(lvl): 100(0) Energy(Start): 800(1000) Torps(lvl): 50(100) TorpDmg: 500 Fighters(lvl): 100 Armor(lvl): 100 Does have Pod? Y', $messages[1]);
        self::assertEquals('TShip Beams(lvl): 100(0) Shields(lvl): 100(0) Energy(Start): 800(1000) Torps(lvl): 50(100) TorpDmg: 500 Fighters(lvl): 100 Armor(lvl): 100 Does have Pod? Y', $messages[2]);

        //print_r($messages);
        //print_r($player->lossesInBattle());
        //print_r($target->lossesInBattle());
    }
}
