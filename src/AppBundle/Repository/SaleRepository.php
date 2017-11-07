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
		    ->where('s.paymentMode LIKE :suspended AND s.deleted = 0')
		    ->setParameter('suspended', "suspended%")
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

    public function loadPurchases($searchText)
    {
    	return $this->createQueryBuilder('s')
           ->where('s.paymentMode LIKE :input')
           ->setParameter('input', '%'.$searchText.'%')
	       ->orderBy('s.id', 'DESC')
           ->getQuery()
           ->getResult();
    }

    public function findAllForThisRange($from, $to, $transaction)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.onDate >= :from AND s.paymentMode LIKE :trans AND s.deleted = 0')
		    ->andWhere('s.onDate < :to')
		    ->setParameter('from', $from)
		    ->setParameter('to', $to)
		    ->setParameter('trans', $transaction.'%')
		    ->getQuery()
		    ->getResult();
    }

    public function findAllForThisDate($startDay, $endDay, $transaction)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.onDate >= :startDay AND s.paymentMode LIKE :trans AND s.deleted = 0')
		    ->andWhere('s.onDate < :endDay')
		    ->setParameter('startDay', $startDay)
		    ->setParameter('endDay', $endDay)
		    ->setParameter('trans', $transaction.'%')
		    ->orderBy('s.onDate', 'ASC')
		    ->getQuery()
		    ->getResult();
    }

    public function countEntries($what, $id = null){
        return $this->createQueryBuilder('s')
            ->select('count(s.id)')
            ->where('s.paymentMode LIKE :what')
            ->andWhere('s.id <= :id')
            ->setParameter('what', $what.'%')
            ->setParameter('id', $id )
            ->getQuery()
            ->getSingleScalarResult();

    }
}