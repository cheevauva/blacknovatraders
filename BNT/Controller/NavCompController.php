<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Link\Servant\LinkSearchPathServant;
use BNT\Exception\WarningException;
use BNT\Exception\SuccessException;

class NavCompController extends BaseController
{

    #[\Override]
    protected function preProcess(): void
    {
        global $allow_navcomp;

        $this->title = $this->l->nav_title;

        if (empty($allow_navcomp)) {
            throw new WarningException('l_nav_nocomp');
        }
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
        $this->render('tpls/navcomp.tpl.php');
    }

    #[\Override]
    protected function processPostAsJson(): void
    {
        $computerTech = $this->playerinfo['computer'];

        if ($computerTech < 5) {
            $maxSearchDepth = 2;
        } elseif ($computerTech < 10) {
            $maxSearchDepth = 3;
        } elseif ($computerTech < 15) {
            $maxSearchDepth = 4;
        } elseif ($computerTech < 20) {
            $maxSearchDepth = 5;
        } else {
            $maxSearchDepth = 6;
        }

        $searchPath = LinkSearchPathServant::new($this->container);
        $searchPath->max_search_depth = $maxSearchDepth;
        $searchPath->stop_sector = $this->fromParsedBody('stop_sector')->notEmpty()->asInt();
        $searchPath->current_sector = $this->playerinfo['sector'];
        $searchPath->serve();

        $links = $searchPath->path;

        if (empty($links)) {
            throw new WarningException('l_nav_proper');
        }

        throw new SuccessException(vsprintf('%s: %s. %s %s %s', [
            $this->l->nav_pathfnd,
            implode(' >> ', array_values($links)),
            $this->l->nav_answ1,
            $searchPath->search_depth,
            $this->l->nav_answ2,
        ]));
    }
}
