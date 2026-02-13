<?php

declare(strict_types=1);

namespace BNT\Traits;

trait DatabaseRowCreateTrait
{

    use DatabaseMainTrait;

    /**
     * @param string $table
     * @param array<string, mixed> $data
     * @return string|null
     */
    protected function rowCreate(string $table, array $data): ?string
    {
        $parameters = [];
        $sets = [];

        foreach ($data as $key => $value) {
            $sets[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        $this->db()->q(sprintf('INSERT INTO %s SET %s', $table, implode(', ', $sets)), $parameters);

        return $this->db()->lastInsertId() ?: throw new \Exception('insert into ' . $table . ' - failed');
    }
}
