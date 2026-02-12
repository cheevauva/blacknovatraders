<?php

declare(strict_types=1);

namespace BNT\Config\DAO;

class ConfigReadDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    /**
     * @var array<string, mixed>
     */
    public $config;

    public function serve(): void
    {
        $this->config = $this->db()->fetchAllKeyValue('SELECT name, value FROM config');
    }
}
