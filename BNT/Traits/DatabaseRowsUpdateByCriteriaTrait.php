<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Container\ContainerInterface;

trait DatabaseRowsUpdateByCriteriaTrait
{

    use DatabaseMainTrait;

    public array $criteria;
    public array $values;

    public function updateRows(string $table): int
    {
        $parameters = [];
        $where = [];
        $sets = [];

        foreach ($this->values as $key => $value) {
            $sets[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        foreach ($this->criteria as $field => $value) {
            $where[] = sprintf('%s = :%s', $field, 'cr' . $field);
            $parameters['cr' . $field] = $value;
        }

        $sql = sprintf('UPDATE %s SET %s WHERE %s', $table, implode(', ', $sets), implode(' AND ', $where));

        return $this->db()->q($sql, $parameters);
    }

    /**
     * @param ContainerInterface $container
     * @param array $values
     * @param array $criteria
     * @return static
     */
    public static function call(ContainerInterface $container, array $values, array $criteria): object
    {
        $self = self::new($container);
        $self->values = $values;
        $self->criteria = $criteria;
        $self->serve();

        return $self;
    }
}
