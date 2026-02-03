<?php

include 'config.php';

try {
    switch (requestMethod()) {
        case 'POST':
            if ($server_closed) {
                $title = $l_login_sclosed;
                throw new \Exception($l_login_closed_message);
            }

            $email = fromPost('email', new \Exception($l_login_email . ' ' . $l_none));
            $pass = fromPost('pass', new \Exception($l_login_pw . ' ' . $l_none));

            $ship = shipByEmail($email);

            if (ipBansCheck($ip)) {
                throw new \Exception($l_login_banned);
            }

            if (empty($ship)) {
                throw new \Exception($l_login_noone);
            }

            if ($ship['password'] !== md5($pass)) {
                playerlog($ship['ship_id'], LOG_BADLOGIN, $ip);
                throw new \Exception("$l_login_4gotpw1 <A HREF=mail.php?mail=$email>$l_clickme</A> $l_login_4gotpw2 <a href=login.php>$l_clickme</a> $l_login_4gotpw3 $ip");
            }

            if ($ship['ship_destroyed'] == 'N') {
                $token = uuidv7();
                setcookie('token', $token, time() + (3600 * 24) * 365, $gamepath, $gamedomain);
                playerlog($ship['ship_id'], LOG_LOGIN, $ip);
                shipSetToken($ship['ship_id'], $token);
                redirectTo('main.php?id=' . $ship['ship_id']);
                die;
            }

            if ($ship['ship_destroyed'] == 'Y' && $ship['dev_escapepod'] == 'Y') {
                shipRestoreEscapepod($ship['ship_id']);
                throw new \Exception($l_login_died);
            }

            $youHaveDied = "You have died in a horrible incident, <a href=log.php>here</a> is the blackbox information that was retrieved from your ships wreckage.";

            if ($ship['ship_destroyed'] == 'Y' && $newbie_nice !== 'YES') {
                throw new \Exception($youHaveDied . ' ' . $l_login_looser);
            }

            if ($ship['ship_destroyed'] == 'Y' && $newbie_nice == 'YES') {
                if (!shipCheckNewbie($ship['ship_id'])) {
                    throw new \Exception($youHaveDied . ' ' . $l_login_looser);
                }

                shipRestoreNewbie($ship['ship_id']);
                throw new \Exception($youHaveDied . ' ' . $l_login_newbie . ' ' . $l_login_newlife);
            }
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