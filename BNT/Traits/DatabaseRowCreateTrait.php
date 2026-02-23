<?php

declare(strict_types=1);

namespace BNT\Traits;

use Psr\Container\ContainerInterface;

trait DatabaseRowCreateTrait
{

    use DatabaseMainTrait;

    public array $data;

    protected function rowCreate(string $table): ?string
    {
        $parameters = [];
        $sets = [];

        foreach ($this->data as $key => $value) {
            $sets[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        $this->db()->q(sprintf('INSERT INTO %s SET %s', $table, implode(', ', $sets)), $parameters);

        if ($this->db()->ErrorMsg()) {
            throw new \Exception($this->db()->ErrorMsg());
        }

        return $this->db()->lastInsertId();
    }

    public static function call(ContainerInterface $container, array $data): self
    {
        $self = self::new($container);
        $self->data = $data;
        $self->serve();

        return $self;
    }
}
