<?php

use BNT\Ship\ShipRanking\DAO\ShipRankingTopScoreDAO;
use BNT\Ship\ShipRanking\DAO\ShipRankingTopTurnsUsedDAO;
use BNT\Ship\ShipRanking\DAO\ShipRankingTopRatingDAO;
use BNT\Ship\View\ShipView;

require_once './config.php';

loadlanguage($lang);

connectdb();

echo twig()->render('ranking/ranking.twig', [
    'shipsAsTopScore' => ShipView::map(ShipRankingTopScoreDAO::call()),
    'shipsAsTopTurnsUsed' => ShipView::map(ShipRankingTopTurnsUsedDAO::call()),
    'shipsAsTopRating' => ShipView::map(ShipRankingTopRatingDAO::call()),
]);
