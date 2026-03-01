<?php

include 'config.php';

BNT\FrontController::call($container, match ($_SERVER['PATH_INFO'] ?? null) {
    '/login' => (function () use ($container) {
        global $server_closed;
        global $gamepath;
        global $gamedomain;

        $login = BNT\Controller\LoginController::new($container);
        $login->serverClosed = $server_closed;
        $login->gamepath = $gamepath;
        $login->gamedomain = $gamedomain;

        return $login;
    })(),
    '/new' => (function () use ($container) {
        global $account_creation_closed;
        global $gamepath;
        global $gamedomain;

        $new = BNT\Controller\NewController::new($container);
        $new->accountCreationClosed = $account_creation_closed;
        $new->gamedomain = $gamedomain;
        $new->gamepath = $gamepath;

        return $new;
    })(),
    '/mines' => BNT\Controller\MinesController::class,
    '/emerwarp' => BNT\Controller\EmergencyWarpController::class,
    '/help' => BNT\Controller\HelpController::class,
    '/genesis' => \BNT\Controller\GenesisController::class,
    '/warpedit' => \BNT\Controller\WarpeditController::class,
    '/zoneedit' => \BNT\Controller\ZoneeditController::class,
    '/zoneinfo' => \BNT\Controller\ZoneinfoController::class,
    '/self_destruct' => \BNT\Controller\SelfDestructController::class,
    '/ships' => BNT\Controller\ShipsController::class,
    '/ship' => BNT\Controller\ShipController::class,
    '/beacon' => BNT\Controller\BeaconController::class,
    '/device' => BNT\Controller\DeviceController::class,
    '/main' => BNT\Controller\MainController::class,
    '/news' => BNT\Controller\NewsController::class,
    '/ranking' => BNT\Controller\RankingController::class,
    '/admin' => match ($_GET['module'] ?? null) {
        'sector' => BNT\Controller\AdminSectorController::class,
        'ship' => BNT\Controller\AdminShipController::class,
        'user' => BNT\Controller\AdminUserController::class,
        'planet' => BNT\Controller\AdminPlanetController::class,
        'zone' => BNT\Controller\AdminZoneController::class,
        'config' => BNT\Controller\AdminConfigController::class,
        default => BNT\Controller\AdminController::class,
    },
    '/logout' => BNT\Controller\LogoutController::class,
    '/options' => BNT\Controller\OptionsController::class,
    '/log' => BNT\Controller\LogController::class,
    '/settings' => BNT\Controller\SettingsController::class,
    '/index' => BNT\Controller\IndexController::class,
    default => BNT\Controller\NotFoundController::class
});
