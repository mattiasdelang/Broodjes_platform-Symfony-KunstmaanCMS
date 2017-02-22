<?php

namespace Kuma\BroodjesBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class TransactionRepository extends EntityRepository
{
    public function getMollieTransaction($mollieId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('b');
        $q = $q = $qb->select('b')
            ->from('KumaBroodjesBundle:Transaction', 'b')
            ->where('b.mollieTransactionId = :mollieId')
            ->andWhere('b.status != :paid')
            ->setParameter('mollieId', $mollieId)
            ->setParameter('paid', 'paid')
            ->getQuery();

        return $q->getResult();
    }
}
