<?php

declare(strict_types=1);

namespace BNT\Traderoute\Entity;

use BNT\Traderoute\Enum\TraderouteTypeEnum;
use BNT\Traderoute\Enum\TraderouteCircuitEnum;
use BNT\Traderoute\Enum\TraderouteMoveTypeEnum;

class Traderoute
{
    public TraderouteTypeEnum $source_type = TraderouteTypeEnum::Port;
    public TraderouteTypeEnum $dest_type = TraderouteTypeEnum::Port;
    public int $traderoute_id;
    public int $source_id = 0;
    public int $dest_id = 0;
    public int $owner = 0;
    public TraderouteCircuitEnum $circuit = TraderouteCircuitEnum::Two;
    public TraderouteMoveTypeEnum $move_type = TraderouteMoveTypeEnum::W;
}
