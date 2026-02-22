<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Container\ContainerInterface;

trait DatabaseRowSelectByCriteriaTrait
{

    use DatabaseMainTrait;

    #[\Override]
    public function selectRow(string $table): array
    {
        $parameters = [];
        $where = [];

        foreach ($this->criteria as $field => $value) {
            $where[] = sprintf('%s = :%s', $field, $field);
            $parameters[$field] = $value;
        }

        return $this->db()->fetchAll('SELECT * FROM ' . $table . ' WHERE ' . implode(' AND ', $where) . ' LIMIT 1', $parameters);
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
