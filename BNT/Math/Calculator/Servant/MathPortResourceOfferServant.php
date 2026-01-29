<?php

declare(strict_types=1);

namespace BNT\Math\Calculator\Servant;

use BNT\Math\DTO\MathSectorDTO;

class MathPortResourceOfferServant implements \BNT\ServantInterface
{

    public MathSectorDTO $sector;
    //
    public $trade_ore = 0;
    public $trade_organics = 0;
    public $trade_goods = 0;
    public $trade_energy = 0;
    //
    public $needle_trade_ore = 0;
    public $needle_trade_organics = 0;
    public $needle_trade_goods = 0;
    public $needle_trade_energy = 0;
    //
    public $ore_price = 0;
    public $organics_price = 0;
    public $goods_price = 0;
    public $energy_price = 0;
    public $total_cost = 0;
    public $cargo_exchanged = 0;

    public function serve(): void
    {
        
    }

    private function calculateTotal()
    {
        return array_sum([
            $this->trade_ore * $this->ore_price,
            $this->trade_organics * $this->organics_price,
            $this->trade_goods * $this->goods_price,
            $this->trade_energy * $this->energy_price,
        ]);
    }

    private function trade(SectorPortTypeEnum $portType, $origin)
    {
        return $this->sector->port_type->is($portType) ? $origin : -$origin;
    }

}
