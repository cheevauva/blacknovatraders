<?php

declare(strict_types=1);

namespace BNT\Ship\Mapper;

use BNT\Ship\Entity\Ship;
use BNT\ServantInterface;

class ShipMapper implements ServantInterface
{
    public ?array $row = null;
    public ?Ship $ship = null;

    public function serve(): void
    {
        if (empty($this->ship) && !empty($this->row)) {
            $ship = $this->ship = new Ship();
            $ship->ship_id = intval($this->row['ship_id']);
            $ship->ship_name = $this->row['ship_name'];
            $ship->ship_destroyed = toBool($this->row['ship_destroyed']);
            $ship->character_name = $this->row['character_name'];
            $ship->password = $this->row['password'];
            $ship->email = $this->row['email'];
            $ship->hull = intval($this->row['hull']);
            $ship->engines = intval($this->row['engines']);
            $ship->power = intval($this->row['power']);
            $ship->computer = intval($this->row['computer']);
            $ship->sensors = intval($this->row['sensors']);
            $ship->beams = intval($this->row['beams']);
            $ship->torp_launchers = intval($this->row['torp_launchers']);
            $ship->torps = intval($this->row['torps']);
            $ship->shields = intval($this->row['shields']);
            $ship->armor = intval($this->row['armor']);
            $ship->armor_pts = intval($this->row['armor_pts']);
            $ship->cloak = intval($this->row['cloak']);
            $ship->credits = intval($this->row['credits']);
            $ship->credits = intval($this->row['credits']);
            $ship->ship_ore = intval($this->row['ship_ore']);
            $ship->ship_organics = intval($this->row['ship_organics']);
            $ship->ship_goods = intval($this->row['ship_goods']);
            $ship->ship_energy = intval($this->row['ship_energy']);
            $ship->ship_colonists = intval($this->row['ship_colonists']);
            $ship->ship_fighters = intval($this->row['ship_fighters']);
            $ship->ship_damage = intval($this->row['ship_damage']);
            $ship->turns = intval($this->row['turns']);
            $ship->on_planet = toBool($this->row['on_planet']);
            $ship->dev_warpedit = intval($this->row['dev_warpedit']);
            $ship->dev_genesis = intval($this->row['dev_genesis']);
            $ship->dev_beacon = intval($this->row['dev_beacon']);
            $ship->dev_emerwarp = intval($this->row['dev_emerwarp']);
            $ship->dev_escapepod = toBool($this->row['dev_escapepod']);
            $ship->dev_fuelscoop = toBool($this->row['dev_fuelscoop']);
            $ship->dev_minedeflector = intval($this->row['dev_minedeflector']);
            $ship->turns_used = intval($this->row['turns_used']);
            $ship->last_login = $this->row['last_login'] ? new \DateTime($this->row['last_login']) : $ship->last_login;
            $ship->rating = intval($this->row['rating']);
            $ship->score = intval($this->row['score']);
            $ship->team = intval($this->row['team']);
            $ship->team_invite = intval($this->row['team_invite']);
            $ship->interface = $this->row['interface'];
            $ship->ip_address = $this->row['ip_address'];
            $ship->planet_id = intval($this->row['planet_id']);
            $ship->preset1 = intval($this->row['preset1']);
            $ship->preset2 = intval($this->row['preset2']);
            $ship->preset3 = intval($this->row['preset3']);
            $ship->trade_colonists = toBool($this->row['trade_colonists']);
            $ship->trade_fighters = toBool($this->row['trade_fighters']);
            $ship->trade_torps = toBool($this->row['trade_torps']);
            $ship->trade_energy = toBool($this->row['trade_energy']);
            $ship->cleared_defences = $this->row['cleared_defences'];
            $ship->lang = $this->row['lang'];
            $ship->dhtml = toBool($this->row['dhtml']);
            $ship->dev_lssd = toBool($this->row['dev_lssd']);
            $ship->sector = intval($this->row['sector']);

            $ship->lang = $this->row['lang'] ?? null;
        }

        if (!empty($this->ship) && empty($this->row)) {
            $ship = $this->ship;
            $row = [];
            $row['ship_name'] = $ship->ship_name;
            $row['ship_destroyed'] = fromBool($ship->ship_destroyed);
            $row['character_name'] = $ship->character_name;
            $row['password'] = $ship->password;
            $row['email'] = $ship->email;
            $row['hull'] = $ship->hull;
            $row['engines'] = $ship->engines;
            $row['power'] = $ship->power;
            $row['computer'] = $ship->computer;
            $row['sensors'] = $ship->sensors;
            $row['beams'] = $ship->beams;
            $row['torp_launchers'] = $ship->torp_launchers;
            $row['torps'] = $ship->torps;
            $row['shields'] = $ship->shields;
            $row['armor'] = $ship->armor;
            $row['armor_pts'] = $ship->armor_pts;
            $row['cloak'] = $ship->cloak;
            $row['credits'] = $ship->credits;
            $row['sector'] = $ship->sector;
            $row['ship_ore'] = $ship->ship_ore;
            $row['ship_organics'] = $ship->ship_organics;
            $row['ship_goods'] = $ship->ship_goods;
            $row['ship_energy'] = $ship->ship_energy;
            $row['ship_colonists'] = $ship->ship_colonists;
            $row['ship_fighters'] = $ship->ship_fighters;
            $row['ship_damage'] = $ship->ship_damage;
            $row['turns'] = $ship->turns;
            $row['on_planet'] = fromBool($ship->on_planet);
            $row['dev_warpedit'] = $ship->dev_warpedit;
            $row['dev_genesis'] = $ship->dev_genesis;
            $row['dev_beacon'] = $ship->dev_beacon;
            $row['dev_emerwarp'] = $ship->dev_beacon;
            $row['dev_escapepod'] = fromBool($ship->dev_escapepod);
            $row['dev_fuelscoop'] = fromBool($ship->dev_fuelscoop);
            $row['dev_minedeflector'] = $ship->dev_minedeflector;
            $row['turns_used'] = $ship->turns_used;
            $row['last_login'] = $ship->last_login->format('Y-m-d H:i:s');
            $row['rating'] = $ship->rating;
            $row['score'] = $ship->score;
            $row['team'] = $ship->team;
            $row['team_invite'] = $ship->team_invite;
            $row['interface'] = $ship->interface;
            $row['ip_address'] = $ship->ip_address;
            $row['planet_id'] = $ship->planet_id;
            $row['preset1'] = $ship->preset1;
            $row['preset2'] = $ship->preset2;
            $row['preset3'] = $ship->preset3;
            $row['trade_colonists'] = fromBool($ship->trade_colonists);
            $row['trade_fighters'] = fromBool($ship->trade_fighters);
            $row['trade_torps'] = fromBool($ship->trade_torps);
            $row['trade_energy'] = fromBool($ship->trade_energy);
            $row['cleared_defences'] = $ship->cleared_defences;
            $row['lang'] = $ship->lang;
            $row['dhtml'] = fromBool($ship->dhtml);
            $row['dev_lssd'] = fromBool($ship->dev_lssd);

            $this->row = $row;
        }
    }
}
