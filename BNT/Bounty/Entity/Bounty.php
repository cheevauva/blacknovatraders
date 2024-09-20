<?php

declare(strict_types=1);

namespace BNT\Bounty\Entity;

class Bounty
{
    use \BNT\Traits\AsTrait;

    public int $bounty_id;
    public int $placed_by;
    public int $bounty_on;
    public int $amount;
}
