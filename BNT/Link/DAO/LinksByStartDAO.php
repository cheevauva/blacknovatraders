<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

use Psr\Container\ContainerInterface;

class LinksByStartDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $start;
    public array $links;

    #[\Override]
    public function serve(): void
    {
        $this->links = $this->db()->fetchAll("SELECT * FROM links WHERE link_start= :sectorId ORDER BY link_dest ASC", [
            'sectorId' => $this->start,
        ]);
    }

    public static function call(ContainerInterface $container, int $start): self
    {
        $self = self::new($container);
        $self->start = $start;
        $self->serve();

        return $self;
    }
}
