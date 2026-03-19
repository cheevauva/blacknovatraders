<?php

declare(strict_types=1);

namespace Tests\Unit\Game\Servant;

use BNT\Ship\Ship;
use BNT\Game\Servant\GameShipFightServant;

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

        return new Ship($ship);
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
        
        self::assertCount(20, $messages);
        
        //print_r($messages);
        //print_r($player->lossesInBattle());
        //print_r($target->lossesInBattle());
    }
}
