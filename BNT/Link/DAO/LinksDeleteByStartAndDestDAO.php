<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

use Psr\Container\ContainerInterface;

class LinksDeleteByStartAndDestDAO extends \UUA\DAO
{

    use \BNT\Traits\DatabaseMainTrait;

    public int $start;
    public int $dest;

    #[\Override]
    public function serve(): void
    {
        $this->db()->q('DELETE FROM links WHERE link_start= :link_start AND link_dest= :link_dest', [
            'link_start' => $this->start,
            'link_dest' => $this->dest,
        ]);
    }

    public static function call(ContainerInterface $container, int $start, int $dest): self
    {
        $self = self::new($container);
        $self->start = $start;
        $self->dest = $dest;
        $self->serve();

        return $self;
    }
}
