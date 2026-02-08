<?php

//declare(strict_types=1);

namespace BNT\Config\DAO;

class ConfigReadDAO extends \UUA\DAO
{

    /**
     * @var array<string, mixed>
     */
    public $config;

    public function serve()
    {
        $this->config = db()->fetchAllKeyValue('SELECT name, value FROM config');
    }
}
