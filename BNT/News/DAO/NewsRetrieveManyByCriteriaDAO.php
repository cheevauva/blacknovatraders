<?php

declare(strict_types=1);

namespace BNT\News\DAO;

class NewsRetrieveManyByCriteriaDAO extends NewsDAO
{
    public array $news;
    public ?\DateTimeImmutable $dateFrom;
    public ?\DateTimeImmutable $dateTo;
    public ?int $limit = null;
    public ?bool $sortByNewsIdDESC;

    #[\Override]
    public function serve(): void
    {
        $qb = $this->db()->createQueryBuilder();
        $qb->select('*');
        $qb->from($this->table());

        if (isset($this->dateFrom)) {
            $qb->andWhere('date >= :dateFrom');
            $qb->setParameter('dateFrom', $this->dateFrom->format('Y-m-d 00:00:00'));
        }

        if (isset($this->dateTo)) {
            $qb->andWhere('date <= :dateTo');
            $qb->setParameter('dateTo', $this->dateFrom->format('Y-m-d 23:59:00'));
        }

        if (isset($this->sortByNewsIdDESC)) {
            $qb->orderBy('news_id', $this->sortByNewsIdDESC ? 'DESC' : 'ASC');
        }

        $qb->setMaxResults($this->limit);

        $this->news = $this->asManyNews($qb->fetchAllAssociative());
    }
}
