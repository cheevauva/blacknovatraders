<?php

declare(strict_types=1);

use BNT\Planet\DAO\PlanetRetrieveManyByCriteriaDAO;
use BNT\Planet\Entity\Planet;

require_once './config.php';

connectdb();

if (isNotAuthorized()) {
    die();
}

$ship = ship();
$PRepType = intval($_GET['PRepType'] ?? 0);

$context = [
    'PRepType' => $PRepType,
    'ship' => $ship,
    'base_ore' => $base_ore,
    'base_organics' => $base_organics,
    'base_credits' => $base_credits,
    'base_goods' => $base_goods,
    'base' => [
        'ore' => $base_ore,
        'organics' => $base_organics,
        'credits' => $base_credits,
        'goods' => $base_goods,
    ],
];

switch ($PRepType) {
    case 1:
        $retrievePlanets = new PlanetRetrieveManyByCriteriaDAO;
        $retrievePlanets->owner = $ship->ship_id;
        $retrievePlanets->serve();

        $total_organics = 0;
        $total_ore = 0;
        $total_goods = 0;
        $total_energy = 0;
        $total_colonists = 0;
        $total_credits = 0;
        $total_fighters = 0;
        $total_torp = 0;
        $total_base = 0;
        $total_corp = 0;
        $total_selling = 0;

        foreach ($retrievePlanets->planets as $planet) {
            $planet = Planet::as($planet);

            $total_organics += $planet->organics;
            $total_ore += $planet->ore;
            $total_goods += $planet->goods;
            $total_energy += $planet->energy;
            $total_colonists += $planet->colonists;
            $total_credits += $planet->credits;
            $total_fighters += $planet->fighters;
            $total_torp += $planet->torps;
            $total_base += ($planet->base ? 1 : 0);
            $total_corp += $planet->corp > 0 ? 1 : 0;
            $total_selling += ($planet->sells ? 1 : 0);
        }

        $context = array_merge($context, [
            'planets' => $retrievePlanets->planets,
            'total' => [
                'organics' => $total_organics,
                'ore' => $total_ore,
                'goods' => $total_goods,
                'energy' => $total_energy,
                'colonists' => $total_colonists,
                'credits' => $total_credits,
                'fighters' => $total_fighters,
                'torp' => $total_torp,
                'base' => $total_base,
                'corp' => $total_corp,
                'selling' => $total_selling,
            ],
        ]);
        break;
    case 2:
        $retrievePlanetsWithBase = new PlanetRetrieveManyByCriteriaDAO;
        $retrievePlanetsWithBase->owner = $ship->ship_id;
        $retrievePlanetsWithBase->base = true;
        $retrievePlanetsWithBase->serve();

        $retrievePlanets = new PlanetRetrieveManyByCriteriaDAO;
        $retrievePlanets->owner = $ship->ship_id;
        $retrievePlanets->serve();

        $total_colonists = 0;
        $total_credits = 0;

        foreach ($retrievePlanets->planets as $planet) {
            $planet = Planet::as($planet);

            $total_colonists += $planet->colonists;
            $total_credits += $planet->credits;
        }

        $context = array_merge($context, [
            'planets' => $retrievePlanets->planets,
            'total' => [
                'colonists' => $total_colonists,
                'credits' => $total_credits,
            ],
        ]);
        break;
}

echo twig()->render('planet_report/planet_report.twig', $context);
