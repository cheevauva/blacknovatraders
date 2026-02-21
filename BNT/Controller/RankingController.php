<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Ship\DAO\ShipsGetRankingDAO;
use BNT\Ship\DAO\ShipGetNotDestroyedExcludeXenobeCountDAO;

class RankingController extends BaseController
{

    public array $ships = [];
    public string $sort = '';
    public int $numPlayers = 0;

    #[\Override]
    protected function init(): void
    {
        $this->enableCheckAuth = false;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        global $max_rank;

        $this->sort = (string) $this->fromQueryParams('sort');

        $getRanking = ShipsGetRankingDAO::new($this->container);
        $getRanking->sort = $this->sort;
        $getRanking->max_rank = $max_rank;
        $getRanking->serve();

        $this->ships = $getRanking->ships;
        $this->numPlayers = ShipGetNotDestroyedExcludeXenobeCountDAO::call($this->container)->count;

        $this->render('tpls/ranking.tpl.php');
    }
}
