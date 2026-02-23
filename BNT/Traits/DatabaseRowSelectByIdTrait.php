<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Container\ContainerInterface;

trait DatabaseRowSelectByIdTrait
{

    use DatabaseMainTrait;

    /**
     * @var int
     */
    public int $id;

    protected function selectRow(string $table, string $idField): ?array
    {
        return $this->db()->fetch(sprintf('SELECT * FROM %s WHERE %s = :id LIMIT 1', $table, $idField), [
            'id' => $this->id,
        ]);
    }

    public static function call(ContainerInterface $container, int $id): self
    {
        $self = self::new($container);
        $self->id = $id;
        $self->serve();

        return $self;
    }
}
