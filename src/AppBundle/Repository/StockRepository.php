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


    public function loadAllStocksFromThisUserWithRange($from, $to, $user)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.user = :user AND s.deleted = 0 AND s.transaction != :sus')
		    ->orderBy('s.onDate', 'Desc')
		    ->andWhere('s.onDate >= :from')
		    ->andWhere('s.onDate < :to')
		    ->setParameter('from', $from)
		    ->setParameter('to', $to)
		    ->setParameter('sus', 'sus')
		    ->setParameter('user', $user)
		    ->orderBy('s.onDate', 'ASC')
		    ->getQuery()
		    ->getResult();

    }

    public function loadAllStocksMovementForThisItem($from, $to, $user, $product)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.user = :user AND s.deleted = 0 AND s.transaction != :sus')
		    ->andWhere('s.product = :product')
		    ->andWhere('s.onDate >= :from')
		    ->andWhere('s.onDate < :to')
		    ->setParameter('from', $from)
		    ->setParameter('to', $to)
		    ->setParameter('sus', 'sus')
		    ->setParameter('user', $user)
		    ->setParameter('product', $product)
		    ->orderBy('s.onDate', 'ASC')
		    ->getQuery()
		    ->getResult();

    }

    public function loadAllStocksFromThisUser($user)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.user = :user AND s.deleted = 0 AND s.transaction != :sus')
		    ->orderBy('s.onDate', 'Desc')
		    ->setParameter('user', $user)
		    ->setParameter('sus', 'sus')
		    ->orderBy('s.onDate', 'ASC')
		    ->getQuery()
		    ->getResult();

    }

    public function loadAllStocksFromThisItem($user, $product)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.user = :user AND s.deleted = 0 AND s.transaction != :sus')
		    ->andWhere('s.product = :product')
		    ->orderBy('s.onDate', 'Desc')
		    ->setParameter('user', $user)
		    ->setParameter('sus', 'sus')
		    ->setParameter('product', $product)
		    ->orderBy('s.onDate', 'ASC')
		    ->getQuery()
		    ->getResult();

    }

    public function findAllFromThisUser($user, $transaction)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.user = :user AND s.transaction = :trans AND s.deleted = 0')
		    ->setParameter('user', $user)
		    ->setParameter('trans', $transaction)
		    ->orderBy('s.onDate', 'ASC')
		    ->getQuery()
		    ->getResult();
    }


    public function findAllForThisDate($startDay, $endDay, $transaction)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.onDate >= :startDay AND s.transaction = :trans AND s.deleted = 0')
		    ->andWhere('s.onDate < :endDay')
		    ->setParameter('startDay', $startDay)
		    ->setParameter('endDay', $endDay)
		    ->setParameter('trans', $transaction)
		    ->orderBy('s.onDate', 'ASC')
		    ->getQuery()
		    ->getResult();
    }


    public function findAllForThisRange($from, $to, $transaction)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.onDate >= :from AND s.transaction = :trans AND s.deleted = 0')
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
		    ->where('s.sale = :sale AND s.deleted = 0')
		    ->setParameter('sale', $sale)
		    ->orderBy('s.onDate', 'ASC')
		    ->getQuery()
		    ->getResult();
    }

    public function calculateStockMovementBeforeThisDate($date, $transaction)
    {
    	return $this->createQueryBuilder('s')
    		->select('s')
		    ->where('s.onDate < :date AND s.transaction = :trans AND s.deleted = 0')
		    ->setParameter('date', $date)
		    ->setParameter('trans', $transaction)
		    ->select('SUM(s.totalCost) AS total')
		    ->getQuery()
		    ->getResult();
    }

    public function calculateStockMovementBeforeThisDateItem($date, $transaction, $product)
    {
    	return $this->createQueryBuilder('s')
    		->select('s')
		    ->where('s.onDate < :date AND s.transaction = :trans AND s.deleted = 0')
		    ->andWhere('s.product = :product')
		    ->setParameter('product', $product)
		    ->setParameter('date', $date)
		    ->setParameter('trans', $transaction)
		    ->select('SUM(s.totalCost) AS total')
		    ->getQuery()
		    ->getResult();
    }

    public function findAllStockForThisProduct($product, $transaction)
    {
    	return $this->createQueryBuilder('s')
    		->select('s')
    		->where('s.product = :product AND s.deleted != 1')
    		->andWhere('s.transaction = :transaction')
    		->setParameter('product', $product)
    		->setParameter('transaction', $transaction)
		    ->select('SUM(s.totalCost) AS total')
    		->getQuery()
    		->getResult();
    }


}