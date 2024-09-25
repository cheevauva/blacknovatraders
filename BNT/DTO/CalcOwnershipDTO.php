<?php

declare(strict_types=1);

namespace BNT\DTO;

class CalcOwnershipDTO
{

    use \BNT\Traits\AsTrait;

    public const TYPE_CORP = 'C';
    public const TYPE_SHIP = 'S';

    public string $type;
    public int $num = 0;
    public int $id;
    public int $team;

}
