<?php

declare(strict_types=1);

namespace BNT\Traderoute;

use BNT\Traderoute\TraderouteTypeEnum;
use BNT\Traderoute\TraderouteCircuitEnum;

class Traderoute
{

    public TraderouteTypeEnum $source_type = TraderouteTypeEnum::Port;
    public TraderouteTypeEnum $dest_type = TraderouteTypeEnum::Port;
    public int $traderoute_id;
    public int $source_id;
    public int $dest_id;
    public int $owner;
    public TraderouteCircuitEnum $circuit;

}
