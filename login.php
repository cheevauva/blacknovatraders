<?php

use BNT\EntryPoint\Servant\EntryPointLoginServant;

$disableRegisterGlobalFix = true;

include 'config.php';

try {
    switch (requestMethod()) {
        case 'POST':
            $email = fromPost('email');
            $pass = fromPost('pass');

            $entryPointLogin = EntryPointLoginServant::_new($container);
            $entryPointLogin->email = $email;
            $entryPointLogin->password = $pass;
            $entryPointLogin->serve();

            setcookie('token', $entryPointLogin->ship['token'], time() + (3600 * 24) * 365, $gamepath, $gamedomain);
            redirectTo('main.php?id=' . $entryPointLogin->ship['ship_id']);
            break;
        case 'GET':
            include 'tpls/login.tpl.php';
            break;
    }
} catch (\Exception $ex) {
    switch (requestMethod()) {
        case 'POST':
            echo responseJsonByException($ex);
            break;
        case 'GET':
            include 'tpls/login.tpl.php';
            break;
    }
}
