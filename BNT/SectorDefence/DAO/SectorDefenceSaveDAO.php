<?php

declare(strict_types=1);

namespace BNT\SectorDefence\DAO;

use BNT\SectorDefence\SectorDefence;

class SectorDefenceSaveDAO extends SectorDefenceDAO
{

    public SectorDefence $defence;

    public function serve(): void
    {
        $mapper = $this->mapper();
        $mapper->defence = $this->defence;
        $mapper->serve();

        $this->db()->update($this->table(), $mapper->row, [
            'ship_id' => $this->defence->defence_id,
        ]);
    }

    public static function call(SectorDefence $defence): self
    {
        $self = new static;
        $self->defence = $defence;
        $self->serve();

        return $self;
    }

}
