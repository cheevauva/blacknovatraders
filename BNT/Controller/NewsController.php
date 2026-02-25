<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\News\DAO\NewsByDateDAO;

class NewsController extends BaseController
{

    public string $startdate;
    public array $news = [];
    public string $previousday;
    public string $nextday;

    #[\Override]
    protected function processGetAsHtml(): void
    {
        if (empty($_GET['startdate'])) {
            $this->startdate = date("Y/m/d");
        } else {
            $this->startdate = date(strval($_GET['startdate']));
        }

        $this->title = $this->l->news_title;
        $this->previousday = date('Y/m/d', strtotime($this->startdate . ' -1 day'));
        $this->nextday = date('Y/m/d', strtotime($this->startdate . ' +1 day'));
        $this->news = NewsByDateDAO::call($this->container, $this->startdate)->news;

        $this->render('tpls/news.tpl.php');
    }
}
