<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

class LinkSearchPathDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $current_sector;
    public int $search_depth;
    public int $stop_sector;
    public ?array $path;

    #[\Override]
    public function serve(): void
    {
        $select = ['a1.link_start AS a0', 'a1.link_dest AS a1'];

        for ($i = 2; $i <= $this->search_depth; $i++) {
            $select[] = sprintf('a%s.link_dest AS a%s', $i, $i);
        }

        $join = [];

        for ($i = 2; $i <= $this->search_depth; $i++) {
            $k = $i - 1;
            $join[] = sprintf('INNER JOIN links AS a%s ON a%s.link_dest = a%s.link_start', $i, $k, $i);
        }

        $where = [
            sprintf('a1.link_start = %s', $this->current_sector),
            sprintf('a%s.link_dest = %s', $this->search_depth, $this->stop_sector),
            'a1.link_dest != a1.link_start',
        ];

        for ($i = 2; $i <= $this->search_depth; $i++) {
            $notIn = ['a1.link_dest', 'a1.link_start'];

            for ($j = 2; $j < $i; $j++) {
                $notIn[] = sprintf('a%s.link_dest', $j);
            }

            $where[] = sprintf('a%s.link_dest NOT IN (%s)', $i, implode(', ', $notIn));
        }

        $orderBy = ['a1.link_start, a1.link_dest'];

        for ($i = 2; $i <= $this->search_depth; $i++) {
            $orderBy[] = sprintf('a%s.link_dest ASC', $i);
        }

        $this->path = $this->db()->fetch(vsprintf("SELECT %s FROM links AS a1 %s WHERE %s ORDER BY %s LIMIT 1", [
            implode(", ", $select),
            implode("\n", $join),
            implode(' AND ', $where),
            implode(', ', $orderBy)
        ]));
    }
}
