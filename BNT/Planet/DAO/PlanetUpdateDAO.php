<?php

declare(strict_types=1);

namespace BNT\Planet\DAO;

use Psr\Container\ContainerInterface;

class PlanetUpdateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $id;

    /**
     * @var array<string, mixed>
     */
    public array $planet;

    #[\Override]
    public function serve(): void
    {
        $parameters = [];
        $values = [];

        foreach ($this->planet as $key => $value) {
            $values[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        $parameters['planet_id'] = $this->id;

        $this->db()->q(sprintf('UPDATE planets SET %s WHERE planet_id = :planet_id', implode(', ', $values)), $parameters);
    }

    /**
     * @param ContainerInterface $container
     * @param array<string, mixed> $planet
     * @param int $id
     * @return self
     */
    public static function call(ContainerInterface $container, array $planet, int $id): self
    {
        $self = self::new($container);
        $self->planet = $planet;
        $self->id = $id;
        $self->serve();

        return $self;
    }
}
