<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Container\ContainerInterface;

trait DatabaseRowCreateTrait
{

    use DatabaseMainTrait;

    public array $data;
    public ?int $id;

    protected function rowCreate(string $table, ?array $data = null): void
    {
        $parameters = [];
        $sets = [];

        foreach ($data ?? $this->data as $key => $value) {
            $sets[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        $this->db()->q(sprintf('INSERT INTO %s SET %s', $table, implode(', ', $sets)), $parameters);

        if ($this->db()->ErrorMsg()) {
            throw new \Exception($this->db()->ErrorMsg());
        }

        $id = $this->db()->lastInsertId();

        $this->id = is_null($id) ? null : (int) $id;
    }

    public static function call(ContainerInterface $container, array $data): self
    {
        $self = self::new($container);
        $self->data = $data;
        $self->serve();

        return $self;
    }
}
