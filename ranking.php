<?php

include("config.php");
include("languages/$lang");
connectdb();

$rows = shipsGetRankingData($sort, $max_rank);
$num_players = shipsGetNotDestroyedExcludeXenobeCount();
$current_sort = $rankingData['sort'];

include 'ranking.tpl.php';
