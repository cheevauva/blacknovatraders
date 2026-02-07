<?php

$disableAutoLogin = true;

include 'config.php';

try {
    switch (requestMethod()) {
        case 'POST':
            if (fromPost('password', new \Exception('password is required')) !== $adminpass) {
                throw new \Exception('wrong password');
            }
            
            $files = glob('schema/*.sql');

            sort($files);

            $messages = [];

            try {
                db()->q('CREATE TABLE IF NOT EXISTS migrations (migration text, date_execution datetime)');

                $migrations = array_map(function ($row) {
                    return $row['migration'];
                }, db()->fetchAll('SELECT migration FROM migrations'));
            } catch (\Exception $ex) {
                $messages[] = $ex->getMessage();
                $migrations = [];
            }

            foreach ($files as $file) {
                $migration = basename($file);

                if (in_array($migration, $migrations, true)) {
                    $messages[] = sprintf('%s skip, already executed', $migration);
                    continue;
                }

                $query = file_get_contents($file);

                try {
                    db()->q($query);
                    db()->q('INSERT INTO migrations SET migration = :migration, date_execution = NOW()', [
                        'migration' => $migration,
                    ]);
                    $messages[] = sprintf('%s - success', $migration);
                } catch (\Exception $ex) {
                    $messages[] = sprintf('%s - fail, %s', $migration, $ex->getMessage());
                }
            }
            include 'tpls/schema_messages.tpl.php';
            break;
        case 'GET':
            include 'tpls/schema_login.tpl.php';
            break;
    }
} catch (\Exception $ex) {
    include 'tpls/error.tpl.php';
}





