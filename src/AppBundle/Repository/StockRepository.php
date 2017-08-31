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
		    ->orderBy('s.onDate', 'Desc')
		    ->getQuery()
		    ->getResult();
    }

    public function findAllFromThisUser($user, $transaction)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.user = :user AND s.transaction = :trans')
		    ->setParameter('user', $user)
		    ->setParameter('trans', $transaction)
		    ->orderBy('s.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }


    public function findAllForThisDate($startDay, $endDay, $transaction)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.onDate >= :startDay AND s.transaction = :trans')
		    ->andWhere('s.onDate < :endDay')
		    ->setParameter('startDay', $startDay)
		    ->setParameter('endDay', $endDay)
		    ->setParameter('trans', $transaction)
		    ->orderBy('s.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }


    public function findAllForThisRange($from, $to, $transaction)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.onDate >= :from AND s.transaction = :trans')
		    ->andWhere('s.onDate < :to')
		    ->setParameter('from', $from)
		    ->setParameter('to', $to)
		    ->setParameter('trans', $transaction)
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