<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

use BNT\Planet\Entity\Planet;

class PlanetSaveDAO extends PlanetDAO
{
    public Planet $planet;

    #[\Override]
    public function serve(): void
    {
        if (!isset($this->planet->planet_id)) {
            $this->db()->insert($this->table(), $this->asRow($this->planet));

            $this->planet->planet_id = (int) $this->db()->lastInsertId();
        } else {
            $this->db()->update($this->table(), $this->asRow($this->planet), [
                'planet_id' => $this->planet->planet_id,
            ]);
        }
    }

    public static function call(\Psr\Container\ContainerInterface $container, Planet $planet): void
    {
        $self = static::new($container);
        $self->planet = $planet;
        $self->serve();
    }
}
