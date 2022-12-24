<?php

use BNT\Ship\Servant\ShipLoginServant;
use BNT\Ship\Exception\ShipNotFoundException;
use BNT\Ship\Exception\ShipPasswordIncorrectException;
use BNT\Ship\Exception\ShipHasBeenDestroyedException;
use BNT\Ship\Servant\ShipRestoreServant;

include 'config.php';
include "languages/$lang";

$playerfound = true;
$incorrectPassword = false;

try {
    $login = new ShipLoginServant;
    $login->email = strval($_POST['email'] ?? '');
    $login->password = strval($_POST['pass'] ?? '');
    $login->ip = $ip;
    $login->serve();

    $ship = $login->ship;
    $correctLogin = true;
    $_SESSION['ship_id'] = $ship->ship_id;
    $_SESSION['ship_lang'] = $ship->lang;
    $_SESSION['interface'] = $mainfilename ?? '';
    header('Location: main.php?id=' . $ship->ship_id);
    die;
} catch (ShipNotFoundException $ex) {
    $playerfound = false;
} catch (ShipPasswordIncorrectException $ex) {
    $incorrectPassword = true;
} catch (ShipHasBeenDestroyedException $ex) {
    $restore = new ShipRestoreServant;
    $restore->ship = $ex->ship;
    $restore->serve();
}

$lang = $ship->language ?? $default_lang;

loadlanguage($lang);

include 'header.php';

bigtitle($l_login_title2);

if (empty($playerfound)) {
    echo "<B>$l_login_noone</B><BR>";
    goto footer;
}

if (!empty($incorrectPassword)) {
    echo "$l_login_4gotpw1 <A HREF=mail.php?mail=$email>$l_clickme</A> $l_login_4gotpw2 <a href=login.php>$l_clickme</a> $l_login_4gotpw3 $ip...";
    goto footer;
}

if ($ship->ship_destroyed) {
    if ($ship->dev_escapepod) {
        echo $l_login_died;
    } else {
        echo "You have died in a horrible incident, <a href=log.php>here</a> is the blackbox information that was retrieved from your ships wreckage.<BR><BR>";
        echo "<BR><BR>$l_login_newbie<BR><BR>";
        echo $l_login_newlife;
    }
    goto footer;
}


footer:
include 'footer.php';
