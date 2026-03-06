<?php

declare(strict_types=1);

namespace BNT\Controller;

use BNT\Exception\WarningException;
use BNT\SectorDefence\DAO\SectorDefencesByCriteriaDAO;

class DefenceReportController extends BaseController
{

    public array $defences = [];

    #[\Override]
    protected function preProcess(): void
    {
        $this->title = $this->l->sdf_title;
    }

    #[\Override]
    protected function processGetAsHtml(): void
    {
//        if (!empty($sort)) {
//            $query .= " ORDER BY";
//            if ($sort == "quantity") {
//                $query .= " quantity ASC";
//            } elseif ($sort == "mode") {
//                $query .= " fm_setting ASC";
//            } elseif ($sort == "type") {
//                $query .= " defence_type ASC";
//            } else {
//                $query .= " sector_id ASC";
//            }
//        }

        $this->defences = SectorDefencesByCriteriaDAO::call($this->container, [
                    'ship_id' => $this->playerinfo['ship_id'],
                ])->defences;

        if (empty($this->defences)) {
            throw new WarningException('l_sdf_none');
        }

        $this->render('tpls/defence_report.tpl.php');
    }
}
