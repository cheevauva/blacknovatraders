<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

use Psr\Container\ContainerInterface;

class ZoneUpdateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $id;

    /**
     * @var array<string, mixed>
     */
    public array $zone;

    #[\Override]
    public function serve(): void
    {
        $parameters = [];
        $values = [];

        foreach ($this->zone as $key => $value) {
            $values[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        $parameters['zone_id'] = $this->id;

        $this->db()->q(sprintf('UPDATE zones SET %s WHERE zone_id = :zone_id', implode(', ', $values)), $parameters);
    }

    /**
     * @param ContainerInterface $container
     * @param array<string, mixed> $zone
     * @param int $id
     * @return self
     */
    public static function call(ContainerInterface $container, array $zone, int $id): self
    {
        $self = self::new($container);
        $self->zone = $zone;
        $self->id = $id;
        $self->serve();

        return $self;
    }
}
