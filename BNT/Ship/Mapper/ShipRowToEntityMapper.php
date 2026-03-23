<?php

declare(strict_types=1);

namespace BNT\Ship\Mapper;

use BNT\Ship\Ship;
use Psr\Container\ContainerInterface;

class ShipRowToEntityMapper extends \UUA\Mapper
{

    public array $row;
    public Ship $ship;

    #[\Override]
    public function serve(): void
    {
        $row = $this->row;

        $ship = new Ship;
        $ship->id = $row['ship_id'];
        $ship->name = $row['ship_name'];
        $ship->energy = $row['ship_energy'];
        $ship->armor_pts = $row['armor_pts'];
        $ship->armor = $row['armor'];
        $ship->fighters = $row['ship_fighters'];
        $ship->ore = $row['ship_ore'];
        $ship->organics = $row['ship_organics'];
        $ship->goods = $row['ship_goods'];
        $ship->engines = $row['engines'];
        $ship->power = $row['power'];
        $ship->computer = $row['computer'];
        $ship->sensors = $row['sensors'];
        $ship->colonists = $row['ship_colonists'];
        $ship->hull = $row['hull'];
        $ship->torp_launchers = $row['torp_launchers'];
        $ship->torps = $row['torps'];
        $ship->cloak = $row['cloak'];
        $ship->sector = $row['sector'];
        $ship->dev_emerwarp = $row['dev_emerwarp'];
        $ship->turns = $row['turns'];
        $ship->turns_used = $row['turns_used'];
        $ship->beams = $row['beams'];
        $ship->shields = $row['shields'];
        $ship->dev_escapepod = $row['dev_escapepod'] == 'Y';
        $ship->cleared_defences = $row['cleared_defences'];

        $this->ship = $ship;
    }

    public static function call(ContainerInterface $container, array $row): self
    {
        $self = self::new($container);
        $self->row = $row;
        $self->serve();

        return $self;
    }
}
