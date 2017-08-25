<?php 

namespace AppBundle\Repository;

class SaleRepository extends \Doctrine\ORM\EntityRepository
{

    public function loadLastSaleEntry()
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->orderBy('s.id', 'DESC')
		    ->setMaxResults(1)
		    ->getQuery()
		    ->getOneOrNullResult();
    }


    public function loadAllSalesFromThisUser($user)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.user = :user AND s.deleted = 0 AND s.paymentMode != :suspended')
		    ->setParameters(array('user' => $user, 'suspended' => "suspended"))
		    ->orderBy('s.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }

    public function findAllSuspended()
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.paymentMode = :suspended AND s.deleted = 0')
		    ->setParameter('suspended', "suspended")
		    ->orderBy('s.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }

    public function loadAllDeletedSalesFromThisUser($user)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.user = :user AND s.deleted = 1')
		    ->setParameter('user', $user)
		    ->orderBy('s.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }


}