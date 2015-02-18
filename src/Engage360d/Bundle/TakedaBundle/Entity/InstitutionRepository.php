<?php

namespace Engage360d\Bundle\TakedaBundle\Entity;

use Engage360d\Bundle\JsonApiBundle\Entity\JsonApiRepository;

class InstitutionRepository extends JsonApiRepository
{
    public function findParsedTowns()
    {
        $parsedTowns = $this->createQueryBuilder('i')
            ->select('i.parsedTown')
            ->distinct()
            ->where('i.parsedTown != :empty')
            ->orderBy('i.parsedTown')
            ->setParameter('empty', "")
            ->getQuery()
            ->getArrayResult();

        return array_map(function($row) { return $row['parsedTown']; }, $parsedTowns);
    }

    public function findSpecializations()
    {
        $specializations = $this->createQueryBuilder('i')
            ->select('i.specialization')
            ->distinct()
            ->where('i.specialization != :empty')
            ->orderBy('i.specialization')
            ->setParameter('empty', "")
            ->getQuery()
            ->getArrayResult();

        return array_map(function($row) { return $row['specialization']; }, $specializations);
    }

    public function filter($parsedTown, $specialization, $limit = null)
    {
        $q = $this->createQueryBuilder('i')
            ->select('i');

        if ($parsedTown && count($parsedTown) > 0) {
            $q = $q->andWhere('i.parsedTown = :parsedTown')
                ->setParameter('parsedTown', $parsedTown);
        }

        if ($specialization && count($specialization) > 0) {
            $q = $q->andWhere('i.specialization = :specialization')
                ->setParameter('specialization', $specialization);
        }

        $q = $q->orderBy('i.priority', 'DESC');

        if ($limit) {
          $q = $q->setMaxResults($limit);
        }

        return $q->getQuery()->getResult();
    }
}
