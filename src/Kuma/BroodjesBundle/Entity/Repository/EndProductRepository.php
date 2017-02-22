<?php
namespace Kuma\BroodjesBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\User;

class EndProductRepository extends EntityRepository
{

    public function unSetDefaults(User $user)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $q = $qb->update('KumaBroodjesBundle:EndProduct', 'b')
            ->set('b.isDefault', 0)
            ->where('b.isDefault = 1')
            ->where('b.user = :user')
            ->setParameter('user',$user)
            ->getQuery();
        $q->execute();
    }

}
