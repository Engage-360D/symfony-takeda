<?php

namespace Engage360d\Bundle\TakedaBundle\Entity;

use Engage360d\Bundle\JsonApiBundle\Entity\JsonApiRepository;

class InstitutionRepository extends JsonApiRepository
{
    public function findParsedTowns()
    {
        $parsedTowns = $this->createQueryBuilder('i')
            ->select('i.parsedTown, COUNT(i.parsedTown) as HIDDEN c')
            ->where('i.parsedTown != :empty')
            ->groupBy('i.parsedTown')
            ->orderBy('c', 'DESC')
            ->setParameter('empty', "")
            ->getQuery()
            ->getArrayResult();

        $parsedTowns = array_map(function($row) { return $row['parsedTown']; }, $parsedTowns);
        $popular = array_splice($parsedTowns, 0, 3);
        sort($parsedTowns);
        $parsedTowns = array_merge($popular, $parsedTowns);
        return $parsedTowns;
    }

    public function findParsedTownByCoords($lat, $lng)
    {
        $parsedTown = $this->createQueryBuilder('i')
            ->select('i.parsedTown')
            ->where('i.lat != 0')
            ->andWhere('i.lng != 0')
            ->andWhere('i.parsedTown != \'\'')
            ->orderBy('ABS(i.lat - :lat) + ABS(i.lng - :lng)', 'ASC')
            ->setMaxResults(1)
            ->setParameter('lat', $lat)
            ->setParameter('lng', $lng)
            ->getQuery()
            ->getSingleScalarResult();

        return $parsedTown;
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
