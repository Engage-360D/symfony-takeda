<?php

namespace Engage360d\Bundle\JsonApiBundle\Entity;

use Doctrine\ORM\EntityRepository;

class JsonApiRepository extends EntityRepository
{
    /**
     * @param null $page
     * @param null $limit
     * @param array $order Sorting options
     *  Format of the $order argument:
     *      $order = array(
     *          array(
     *              'property' = > 'createdAt',
     *              'direction' => 'ASC',
     *          ),
     *      )
     * @param string $where
     *
     * @return array
     */
    public function findSubset($page = null, $limit = null, $order = array(), $where = '')
    {
        $qb = $this->createQueryBuilder('entity');

        if ($where) {
            $qb->where('entity.' . $where);
        }

        foreach ($order as $set) {
            $qb->orderBy('entity.' . $set['property'], $set['direction']);
        }

        if ($page && $limit) {
            $qb->setFirstResult(($page - 1) * $limit)
                ->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}