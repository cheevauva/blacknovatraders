<?php

declare(strict_types=1);

namespace BNT\Migration\DAO;

class MigrationsFindAllDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;
    use \BNT\Traits\UnitSimpleCallTrait;

    public $migrations;

    public function serve(): void
    {
        $files = glob('schema/*.sql');

        sort($files);

        $this->migrations = [];

        foreach ($files as $file) {
            $this->migrations[] = [
                'migration' => basename($file),
                'query' => file_get_contents($file),
            ];
        }
    }
}
