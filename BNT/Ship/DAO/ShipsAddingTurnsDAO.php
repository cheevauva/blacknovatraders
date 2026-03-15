<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipsAddingTurnsDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $multiplier;

    #[\Override]
    public function serve(): void
    {
        global $max_turns;

        $this->db()->q('UPDATE ships SET turns=turns + (1 * :multiplier) WHERE turns < :max_turns', [
            'multiplier' => $this->multiplier,
            'max_turns' => $max_turns,
        ]);
    }
}
