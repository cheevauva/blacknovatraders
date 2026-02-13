<?php

declare(strict_types=1);

namespace BNT\Config\DAO;

class ConfigReadDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;
    use \BNT\Traits\UnitSimpleCallTrait;

    /**
     * @var array<string, mixed>
     */
    public array $config;

    #[\Override]
    public function serve(): void
    {
        $this->config = $this->db()->fetchAllKeyValue('SELECT name, value FROM config');
    }
}
