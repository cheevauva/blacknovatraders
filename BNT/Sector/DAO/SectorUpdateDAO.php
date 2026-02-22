<?php

declare(strict_types=1);

namespace BNT\Sector\DAO;

use Psr\Container\ContainerInterface;

class SectorUpdateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $id;

    /**
     * @var array<string, mixed>
     */
    public array $sector;

    #[\Override]
    public function serve(): void
    {
        $parameters = [];
        $values = [];

        foreach ($this->sector as $key => $value) {
            $values[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        $parameters['sector_id'] = $this->id;

        $this->db()->q(sprintf('UPDATE universe SET %s WHERE sector_id = :sector_id', implode(', ', $values)), $parameters);
    }

    /**
     * @param ContainerInterface $container
     * @param array<string, mixed> $sector
     * @param int $id
     * @return self
     */
    public static function call(ContainerInterface $container, array $sector, int $id): self
    {
        $self = self::new($container);
        $self->sector = $sector;
        $self->id = $id;
        $self->serve();

        return $self;
    }
}
