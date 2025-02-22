<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

use BNT\Sector\Entity\Sector;

class SectorSaveDAO extends SectorDAO
{
    public Sector $sector;

    public function serve(): void
    {
        $mapper = $this->mapper();
        $mapper->sector = $this->sector;
        $mapper->serve();

        if (!isset($this->sector->sector_id)) {
            $this->db()->insert($this->table(), $mapper->row);
            $this->sector->sector_id = (int) $this->db()->lastInsertId();
        } else {
            $this->db()->update($this->table(), $mapper->row, [
                'sector_id' => $this->sector->sector_id,
            ]);
        }
    }

    public static function call(\Psr\Container\ContainerInterface $container, Sector $sector): self
    {
        $self = static::new($container);
        $self->sector = $sector;
        $self->serve();

        return $self;
    }
}
