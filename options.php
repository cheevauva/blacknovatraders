<?php
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
                shipUpdateLang($playerinfo['ship_id'], $newlang);
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

                    if (!shipUpdatePassword($playerinfo['ship_id'], md5($newpass1))) {
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

