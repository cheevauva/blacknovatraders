<?php

use BNT\Ship\Servant\ShipLoginServant;
use BNT\Ship\Exception\ShipHasBeenDestroyedException;
use BNT\Ship\Servant\ShipRestoreServant;

require_once 'config.php';

loadlanguage($language);

try {
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
    } catch (ShipHasBeenDestroyedException $ex) {
        $restore = new ShipRestoreServant;
        $restore->ship = $ex->ship;
        $restore->serve();

        throw $ex;
    }
} catch (Exception $ex) {
    echo twig()->render('login/login2.twig', [
        'message' => $ex->getMessage(),
    ]);
}
