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

        if ($this->defence->defence_id) {
            $this->db()->update($this->table(), $mapper->row, [
                'defence_id' => $this->defence->defence_id,
            ]);
        } else {
            $this->db()->insert($this->table(), $mapper->row);
            $this->defence->defence_id = (int) $this->db()->lastInsertId();
        }
    }

    public static function call(SectorDefence $defence): self
    {
        $self = new static;
        $self->defence = $defence;
        $self->serve();

        return $self;
    }

}
