<?php

use BNT\Ship\DAO\ShipUpdateDAO;

$disableRegisterGlobalFix = false;

include 'config.php';

if (checkship()) {
    die();
}

switch (requestMethod()) {
    case 'POST':
        try {
            $oldpass = fromPOST('oldpass');
            $newpass1 = fromPOST('newpass1');
            $newpass2 = fromPOST('newpass2');
            $newlang = fromPOST('newlang', $language);

            if (in_array($newlang, array_keys(languages()), true)) {
                $playerinfo['lang'] = $newlang;

                ShipUpdateDAO::call($container, $playerinfo, $playerinfo['ship_id']);
            }

            if (!empty($newpass1) || !empty($newpass2)) {
                if (empty($oldpass)) {
                    throw new \Exception($l_opt2_srcpassfalse);
                } elseif ($newpass1 != $newpass2) {
                    throw new \Exception($l_opt2_newpassnomatch);
                } else {
                    if (md5($oldpass) != $playerinfo['password']) {
                        throw new \Exception($l_opt2_srcpassfalse);
                    }

                    $playerinfo['password'] = md5($newpass1);

                    ShipUpdateDAO::call($container, $playerinfo, $playerinfo['ship_id']);

                    if (!$shipUpdated) {
                        throw new \Exception($l_opt2_passchangeerr);
                    }
                }
            }

            redirectTo('index.php');
        } catch (\Exception $ex) {
            echo responseJsonByException($ex);
        }
        break;
    case 'GET':
        include 'tpls/options.tpl.php';
        break;
}
