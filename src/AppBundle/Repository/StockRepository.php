<?php 

namespace AppBundle\Repository;

class StockRepository extends \Doctrine\ORM\EntityRepository
{

    public function loadLastStockEntry()
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->orderBy('s.id', 'DESC')
		    ->setMaxResults(1)
		    ->getQuery()
		    ->getOneOrNullResult();
    }


    public function loadAllStocksFromThisUser($user)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.user = :user AND s.deleted = 0')
		    ->setParameter('user', $user)
		    ->orderBy('s.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }


    public function findAllForThisSale($sale)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.sale = :sale')
		    ->setParameter('sale', $sale)
		    ->orderBy('s.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }



}