<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

use Psr\Container\ContainerInterface;

class ShipUpdateDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $id;

    /**
     * @var array<string, mixed>
     */
    public $ship;

    #[\Override]
    public function serve(): void
    {
        $parameters = [];
        $values = [];

        foreach ($this->ship as $key => $value) {
            $values[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        $parameters['ship_id'] = $this->id;

        $this->db()->q(sprintf('UPDATE ships SET %s WHERE ship_id = :ship_id', implode(', ', $values)), $parameters);
    }

    public static function call(ContainerInterface $container, array $ship, int $id): self
    {
        $self = self::new($container);
        $self->ship = $ship;
        $self->id = $id;
        $self->serve();

        return $self;
    }
}
