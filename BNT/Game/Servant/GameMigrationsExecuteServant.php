<?php

declare(strict_types=1);

namespace BNT\Game\Servant;

use BNT\Migration\DAO\MigrationsCreateTableDAO;
use BNT\Migration\DAO\MigrationsFindExcecutedDAO;
use BNT\Migration\DAO\MigrationsFindAllDAO;
use BNT\Migration\DAO\MigrationExecuteDAO;

class GameMigrationsExecuteServant extends \UUA\Servant
{

    use \BNT\Traits\UnitSimpleCallTrait;

    public array $messages;

    #[\Override]
    public function serve(): void
    {
        global $l_schema_skip_already_executed;
        global $l_schema_fail;
        global $l_schema_success;

        $this->messages = [];

        try {
            MigrationsCreateTableDAO::call($this->container);

            $migrationsExecuted = array_map(function ($row) {
                return $row['migration'];
            }, MigrationsFindExcecutedDAO::call($this->container)->migrations);
        } catch (\Exception $ex) {
            $this->messages[] = $ex->getMessage();
            $migrationsExecuted = [];
        }

        $migrations = MigrationsFindAllDAO::call($this->container)->migrations;

        foreach ($migrations as $migration) {
            if (in_array($migration['migration'], $migrationsExecuted, true)) {
                $this->messages[] = sprintf('%s %s', $migration['migration'], $l_schema_skip_already_executed);
                continue;
            }

            try {
                $execute = MigrationExecuteDAO::new($this->container);
                $execute->migration = $migration;
                $execute->serve();

                $this->messages[] = sprintf('%s - %s', $migration['migration'], $l_schema_success);
            } catch (\Exception $ex) {
                $this->messages[] = sprintf('%s - %s, %s', $migration['migration'], $l_schema_fail, $ex->getMessage());
            }
        }
    }
}
