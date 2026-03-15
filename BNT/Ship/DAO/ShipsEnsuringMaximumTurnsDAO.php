<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipsEnsuringMaximumTurnsDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;
    use \BNT\Traits\UnitSimpleCallTrait;

    public int $multiplier;

    #[\Override]
    public function serve(): void
    {
        global $max_turns;

        $this->db()->q('UPDATE ships SET turns = :max_turns WHERE turns > :max_turns', [
            'max_turns' => $max_turns,
        ]);
    }
}
