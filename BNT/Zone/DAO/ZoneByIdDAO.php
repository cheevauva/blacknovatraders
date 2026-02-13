<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

use Psr\Container\ContainerInterface;

class ZoneByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $id;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $zone;

    #[\Override]
    public function serve(): void
    {
        $this->zone = $this->db()->fetch('SELECT * FROM zones WHERE zone_id = :zoneId', [
            'zoneId' => $this->id,
        ]);
    }

    public static function call(ContainerInterface $container, int $id): self
    {
        $self = self::new($container);
        $self->id = $id;
        $self->serve();

        return $self;
    }
}
