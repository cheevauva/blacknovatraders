<?php

use BNT\EntryPoint\Servant\EntryPointSchemaServant;

$disableAutoLogin = true;
$disableRegisterGlobalFix = true;

include 'config.php';

try {
    switch (requestMethod()) {
        case 'POST':
            if (fromPost('password', new \Exception($l_schema_password . ' ' . $l_is_required)) !== $adminpass) {
                throw new \Exception($l_schema_password . ' ' . $l_is_wrong);
            }
            
            $messages = EntryPointSchemaServant::call($container)->messages;

            include 'tpls/schema_messages.tpl.php';
            break;
        case 'GET':
            include 'tpls/schema_login.tpl.php';
            break;
    }
} catch (\Exception $ex) {
    include 'tpls/error.tpl.php';
}





