<?php

declare(strict_types=1);

namespace BNT\Migration\DAO;

class MigrationsFindExcecutedDAO extends \UUA\DAO
{
    use \BNT\Traits\DatabaseMainTrait;
    use \BNT\Traits\UnitSimpleCallTrait;

    public $migrations;

    public function serve(): void
    {
        $this->migrations = $this->db()->fetchAll('SELECT * FROM migrations');
    }
}
