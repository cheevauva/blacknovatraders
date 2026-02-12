<?php

use BNT\EntryPoint\Servant\EntryPointNewServant;

include 'config.php';

if (!checklogin(false)) {
    redirectTo('index.php');
    return;
}

try {

    if ($account_creation_closed) {
        throw new \Exception($l_new_closed_message);
    }

    switch (requestMethod()) {
        case 'POST':
            $entryPointNew = EntryPointNewServant::new($container);
            $entryPointNew->username = fromPost('username', new \Exception($l_new_username . ' ' . $l_is_required));
            $entryPointNew->character = fromPost('character', new \Exception($l_new_character . ' ' . $l_is_required));
            $entryPointNew->shipname = fromPost('shipname', new \Exception($l_new_shipname . ' ' . $l_is_required));
            $entryPointNew->password = fromPost('password', new \Exception($l_new_password . ' ' . $l_is_required));
            $entryPointNew->serve();

            setcookie('token', $entryPointNew->ship['token'], time() + (3600 * 24) * 365, $gamepath, $gamedomain);
            redirectTo('main.php?id=' . $entryPointNew->ship['ship_id']);
            break;
        case 'GET':
            include 'tpls/new.tpl.php';
            break;
    }
} catch (\Exception $ex) {
    switch (requestMethod()) {
        case 'POST':
            echo responseJsonByException($ex);
            break;
        case 'GET':
            include 'tpls/new.tpl.php';

            break;
    }
}
