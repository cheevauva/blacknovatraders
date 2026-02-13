<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

use Psr\Container\ContainerInterface;

class SectorByIdDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $id;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $sector;

    #[\Override]
    public function serve(): void
    {
        $this->sector = $this->db()->fetch('SELECT * FROM universe WHERE sector_id = :sectorId LIMIT 1', [
            'sectorId' => $this->id,
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
