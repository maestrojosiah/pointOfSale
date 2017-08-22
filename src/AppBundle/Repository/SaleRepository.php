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
		    ->where('s.user = :user AND s.deleted = 0')
		    ->setParameter('user', $user)
		    ->orderBy('s.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }



}