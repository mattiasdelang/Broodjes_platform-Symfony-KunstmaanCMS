<?php

namespace Kuma\BroodjesBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Kuma\BroodjesBundle\Entity\EndProduct;
use Kunstmaan\AdminBundle\Entity\User;

class LunchOrderRepository extends EntityRepository
{
    public function isOrderToday(User $user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT d FROM KumaBroodjesBundle:LunchOrder d 
                WHERE DATE(d.date) = CURRENT_DATE() AND d.user = :currentuser AND d.status = 0'
            )
            ->setParameter('currentuser', $user)
            ->getResult();
    }

    public function setStatus()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $q = $qb->update('KumaBroodjesBundle:LunchOrder', 'b')
            ->set('b.status', 1)
            ->where('b.status = 0')
            ->getQuery();
        $q->execute();
    }

    public function endProdInOpenOrder(EndProduct $endProduct)
    {
        $qb = $this->getEntityManager()->createQueryBuilder('b');
        $q = $qb->select('b')
            ->from('KumaBroodjesBundle:LunchOrder', 'b')
            ->join('b.endProducts', 'e')
            ->where('e.id = :prodId')
            ->andWhere('b.status = 0')
            ->setParameter('prodId', $endProduct->getId())
            ->getQuery();

        return $q->execute();
    }
}
