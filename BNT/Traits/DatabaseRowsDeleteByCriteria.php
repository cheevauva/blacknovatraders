<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Container\ContainerInterface;

trait DatabaseRowsDeleteByCriteria
{

    use DatabaseMainTrait;

    public array $criteria;

    public function deleteRows(string $table): array
    {
        $parameters = [];
        $where = [];

        foreach ($this->criteria as $field => $value) {
            $where[] = sprintf('%s = :%s', $field, $field);
            $parameters[$field] = $value;
        }

        $sql = sprintf('DELETE FROM %s WHERE %s', $table, implode(' AND ', $where));

        return $this->db()->q($sql, $parameters);
    }

    /**
     * @param ContainerInterface $container
     * @param array $criteria
     * @return static
     */
    public static function call(ContainerInterface $container, array $criteria): object
    {
        $self = self::new($container);
        $self->criteria = $criteria;
        $self->serve();

        return $self;
    }
}
