<?php

namespace Engage360d\Bundle\TakedaBundle\Entity\PressCenter;

use Engage360d\Bundle\JsonApiBundle\Entity\JsonApiRepository;

class ArticleRepository extends JsonApiRepository
{
    // TODO add method findAllForLast12Months
    public function findAllForLast12Months($onlyActive)
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->where('entity.createdAt > :date');
        $yearAgo = (new \DateTime())->sub(new \DateInterval('P1Y'));
        $qb->setParameter('date', $yearAgo);

        if ($onlyActive) {
            $qb->andWhere('entity.isActive = TRUE');
        }

        $qb->orderBY('entity.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }


    public function findActiveForLast12Months()
    {
        return $this->findAllForLast12Months(true);
    }

    public function findAllByCategory($category, $onlyActive = true)
    {
        $qb = $this->createQueryBuilder('entity');
        $qb->innerJoin('entity.category',  'category');

        $qb->where('category.id = ?categoryId');
        $qb->setParameter('categoryId', $category);

        if ($onlyActive) {
            $qb->andWhere('entity.isActive = TRUE');
        }

        $qb->orderBY('entity.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findAllByDate($date, $onlyActive = true)
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->where('DATE(entity.createdAt) = DATE(:date)');
        $qb->setParameter('date', $date);

        if ($onlyActive) {
            $qb->andWhere('entity.isActive = TRUE');
        }

        $qb->orderBY('entity.createdAt', 'DESC');

        return $qb->getQuery()->getResult();
    }

    public function findLastFourExceptOne($articleId, $onlyActive = true)
    {
        $qb = $this->createQueryBuilder('entity');

        $qb->where('entity.id != :articleId');
        $qb->setParameter('articleId', $articleId);

        if ($onlyActive) {
            $qb->andWhere('entity.isActive = TRUE');
        }

        $qb->orderBY('entity.createdAt', 'DESC');

        $qb->setMaxResults(4);

        return $qb->getQuery()->getResult();
    }
}