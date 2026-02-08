<?php

//declare(strict_types=1);

namespace BNT\Traits;

trait DatabaseRowCreateTrait
{

    use DatabaseMainTrait;

    protected function rowCreate($table, $data)
    {
        $parameters = [];
        $sets = [];

        foreach ($data as $key => $value) {
            $sets[] = sprintf('%s = :%s', $key, $key);
            $parameters[$key] = $value;
        }

        $this->db()->q(sprintf('INSERT INTO %s SET %s', $table, implode(', ', $sets)), $parameters);

        return $this->db()->lastInsertId();
    }
}
