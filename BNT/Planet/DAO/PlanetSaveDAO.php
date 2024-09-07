<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

use BNT\Planet\Planet;

class PlanetSaveDAO extends PlanetDAO
{

    public Planet $planet;

    public function serve(): void
    {
        $mapper = $this->mapper();
        $mapper->planet = $this->planet;
        $mapper->serve();

        $this->db()->update($this->table(), $mapper->row, [
            'planet_id' => $this->planet->planet_id,
        ]);
    }

    public static function call(Planet $planet): void
    {
        $self = new static;
        $self->planet = $planet;
        $self->serve();
    }

}
