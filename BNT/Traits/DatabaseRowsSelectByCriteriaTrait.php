<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Container\ContainerInterface;
use BNT\DB\Criteria\Criteria;

trait DatabaseRowsSelectByCriteriaTrait
{

    use DatabaseMainTrait;

    public array $criteria;
    public array $items;

    public function selectRows(string $table): array
    {
        if (empty($this->criteria)) {
            return $this->items = $this->db()->fetchAll('SELECT * FROM ' . $table);
        }

        $parameters = [];
        $where = [];

        foreach ($this->criteria as $field => $value) {
            if ($value instanceof Criteria) {
                $value->field = $field;
                
                $where[] = strval($value);
                $parameters[$field] = $value->value;
            } else {
                $where[] = sprintf('%s = :%s', $field, $field);
                $parameters[$field] = $value;
            }
        }

        return $this->items = $this->db()->fetchAll('SELECT * FROM ' . $table . ' WHERE ' . implode(' AND ', $where), $parameters);
    }

    /**
     * @param ContainerInterface $container
     * @param array $criteria
     * @return static
     */
    public static function call(ContainerInterface $container, array $criteria = []): object
    {
        $self = self::new($container);
        $self->criteria = $criteria;
        $self->serve();

        return $self;
    }
}
