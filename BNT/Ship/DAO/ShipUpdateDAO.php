<?php

declare(strict_types=1);

namespace BNT\Ship\DAO;

class ShipUpdateDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;

    public $id;

    /**
     * @var array
     */
    public $ship;

    public function serve(): void
    {
        $parameters = [];
        $values = [];

        foreach ($this->ship as $key => $value) {
            $values[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        if (empty($parameters['ship_id'])) {
            $parameters['ship_id'] = $this->id;
        }

        $this->db()->q(sprintf('UPDATE ships SET %s WHERE ship_id = :ship_id', implode(', ', $values)), $parameters);
    }

    /**
     * @param type $container
     * @param string $email
     * @return self
     */
    public static function call($container, $ship, $id = null)
    {
        $self = self::new($container);
        $self->ship = $ship;
        $self->id = $id;
        $self->serve();
    }
}
