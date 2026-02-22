<?php

declare(strict_types=1);

namespace BNT\Zone\DAO;

use Psr\Container\ContainerInterface;

class ZoneCreateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseRowCreateTrait;

    /**
     * @var array<string, mixed>
     */
    public array $zone;
    public protected(set) int $id;

    #[\Override]
    public function serve(): void
    {
        $this->id = $this->zone['zone_id'] = (int) $this->rowCreate('zones', $this->zone);
    }

    /**
     * @param ContainerInterface $container
     * @param array<string, mixed> $zone
     * @return self
     */
    public static function call(ContainerInterface $container, array $zone): self
    {
        $self = self::new($container);
        $self->zone = $zone;
        $self->serve();

        return $self;
    }
}
