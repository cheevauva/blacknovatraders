<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

class PlanetByIdDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;

    public $id;
    public $planet;

    public function serve(): void
    {
        $this->planet = $this->db()->fetch('SELECT * FROM planets WHERE planet_id = :id', [
            'id' => $this->id,
        ]);
    }
}
