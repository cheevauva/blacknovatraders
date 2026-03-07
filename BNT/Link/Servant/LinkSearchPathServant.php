<?php

declare(strict_types=1);

namespace BNT\Link\Servant;

use BNT\Link\DAO\LinkSearchPathDAO;

class LinkSearchPathServant extends \UUA\Servant
{

    public int $max_search_depth;
    public int $current_sector;
    public int $stop_sector;
    public int $search_depth;
    public array $path = [];

    #[\Override]
    public function serve(): void
    {
        for ($search_depth = 1; $search_depth <= $this->max_search_depth; $search_depth++) {
            $this->search_depth = $search_depth;
            
            $searchPath = LinkSearchPathDAO::new($this->container);
            $searchPath->search_depth = $search_depth;
            $searchPath->current_sector = $this->current_sector;
            $searchPath->stop_sector = $this->stop_sector;
            $searchPath->serve();

            if ($searchPath->path) {
                $this->path = $searchPath->path;
                break;
            }
        }
    }
}
