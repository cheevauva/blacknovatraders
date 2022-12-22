<?php

declare(strict_types=1);

namespace BNT\Link\DAO;

class LinkRetrieveManyBySectorDAO extends LinkDAO
{

    public int $sector;
    public array $links;

    public function serve(): void
    {
        $this->links = [];

        $links = $this->db()->fetchAllAssociative("SELECT * FROM {$this->table()} WHERE link_start=:sector ORDER BY link_dest ASC", [
            'sector' => $this->sector
        ]) ?: [];

        foreach ($links as $link) {
            $mapper = $this->mapper();
            $mapper->row = $link;
            $mapper->serve();

            $this->links[] = $mapper->link;
        }
    }

    public static function call(int $sector): array
    {
        $self = new static;
        $self->sector = $sector;
        $self->serve();

        return $self->links;
    }

}
