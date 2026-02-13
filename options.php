<?php

use BNT\ShipFunc;
use BNT\Ship\DAO\ShipUpdateDAO;

$disableRegisterGlobalFix = false;

include 'config.php';

if (checklogin()) {
    die();
}

switch (requestMethod()) {
    case 'POST':
        try {
            $oldpass = fromPost('oldpass');
            $newpass1 = fromPost('newpass1');
            $newpass2 = fromPost('newpass2');
            $newlang = fromPost('newlang', $language);

            if (in_array($newlang, array_keys(languages()), true)) {
                $playerinfo['lang'] = $newlang;

                ShipUpdateDAO::call($container, $playerinfo);
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

                    ShipUpdateDAO::call($container, $playerinfo);

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
