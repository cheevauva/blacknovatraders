<?php

declare(strict_types=1);

namespace BNT\Config\Servant;

use BNT\Config\DAO\ConfigReadDAO;

class ConfigReloadGlobalVarsServant extends \UUA\Servant
{

    #[\Override]
    public function serve(): void
    {
        try {
            $config = ConfigReadDAO::call($this->container)->config;
        } catch (\Exception $ex) {
            $config = [];
        }

        foreach ($config as $name => $value) {
            if (!isset($GLOBALS[$name])) {
                continue;
            }
            $tmpType = gettype($GLOBALS[$name]);
            $GLOBALS[$name] = $value;
            settype($GLOBALS[$name], $tmpType);
        }
    }
}
