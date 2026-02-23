<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Container\ContainerInterface;

trait DatabaseRowsSelectByCriteriaTrait
{

    use DatabaseMainTrait;

    public array $criteria;
    public array $items;
    
    public function selectRows(string $table): array
    {
        $parameters = [];
        $where = [];

        foreach ($this->criteria as $field => $value) {
            $where[] = sprintf('%s = :%s', $field, $field);
            $parameters[$field] = $value;
        }

        $this->items = $this->db()->fetchAll('SELECT * FROM ' . $table . ' WHERE ' . implode(' AND ', $where), $parameters);

        return $this->items;
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
