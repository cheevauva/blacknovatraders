<?php

declare(strict_types=1);

namespace BNT\Config\DAO;

class ConfigUpdateDAO extends \UUA\DAO
{

    /**
     * @var array<string, mixed>
     */
    public $config;

    public function serve(): void
    {
        foreach ($this->config as $name => $value) {
            db()->q('REPLACE INTO config SET value = :value , name = :name', [
                'name' => $name,
                'value' => $value,
            ]);
        }
    }
}
