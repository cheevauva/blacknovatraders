<?php

use BNT\Ship\DAO\ShipRanking\ShipRankingTopScoreDAO;
use BNT\Ship\DAO\ShipRanking\ShipRankingTopTurnsUsedDAO;
use BNT\Ship\DAO\ShipRanking\ShipRankingTopRatingDAO;
use BNT\Ship\View\ShipView;

require_once './config.php';

loadlanguage($lang);

connectdb();

echo twig()->render('ranking/ranking.twig', [
    'shipsAsTopScore' => ShipView::map(ShipRankingTopScoreDAO::call()),
    'shipsAsTopTurnsUsed' => ShipView::map(ShipRankingTopTurnsUsedDAO::call()),
    'shipsAsTopRating' => ShipView::map(ShipRankingTopRatingDAO::call()),
]);
