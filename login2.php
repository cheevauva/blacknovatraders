<?php

include("config.php");
include("languages/$lang");

try {
    connectdb();

    if ($server_closed) {
        $title = $l_login_sclosed;
        throw new \Exception($l_login_closed_message);
    }

    $title = $l_login_title2;

    $email = fromPost('email', new \Exception($l_login_email . ' ' . $l_none));
    $pass = fromPost('pass', new \Exception($l_login_pw . ' ' . $l_none));

    $playerinfo = sqlGetPlayerByEmail($email);
    $playerfound = !empty($playerinfo);

    if (sqlCheckIpBan($ip)) {
        throw new \Exception($l_login_banned);
    }

    $youHaveDied = "You have died in a horrible incident, <a href=log.php>here</a> is the blackbox information that was retrieved from your ships wreckage.<BR><BR>";

    if (!$playerfound) {
        throw new \Exception($l_login_noone);
    }

    if ($playerinfo['password'] !== $pass) {
        playerlog($playerinfo['ship_id'], LOG_BADLOGIN, $ip);
        throw new \Exception("$l_login_4gotpw1 <A HREF=mail.php?mail=$email>$l_clickme</A> $l_login_4gotpw2 <a href=login.php>$l_clickme</a> $l_login_4gotpw3 $ip");
    }

    if ($playerinfo['ship_destroyed'] == 'N') {
        $token = uuidv7();
        setcookie('token', $token, time() + (3600 * 24) * 365, $gamepath, $gamedomain);
        playerlog($playerinfo['ship_id'], LOG_LOGIN, $ip);
        sqlUpdateLogin($playerinfo['ship_id'], $token);
        header('Location:' . "main.php?id=" . $playerinfo['ship_id']);
        die;
    }

    if ($playerinfo['ship_destroyed'] == 'Y' && $playerinfo['dev_escapepod'] == 'Y') {
        sqlRestoreShipEscapepod($playerinfo['ship_id']);
        throw new \Exception($l_login_died);
    }

    if ($playerinfo['ship_destroyed'] == 'Y' && $newbie_nice !== 'YES') {
        throw new \Exception($youHaveDied . $l_login_looser);
    }

    if ($playerinfo['ship_destroyed'] == 'Y' && $newbie_nice == 'YES') {
        if (!sqlCheckNewbieShip($playerinfo['ship_id'])) {
            throw new \Exception($youHaveDied . $l_login_looser);
        }

        sqlRestoreNewbieShip($playerinfo['ship_id']);
        throw new \Exception($youHaveDied . "<BR><BR>$l_login_newbie<BR><BR>" . $l_login_newlife);
    }
} catch (\Exception $ex) {
    include("header.php");

    bigtitle();

    echo "<B>{$ex->getMessage()}</B><BR>";
    include("footer.php");
}
