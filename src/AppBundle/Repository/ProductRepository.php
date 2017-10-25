<?php 

namespace AppBundle\Repository;

class ProductRepository extends \Doctrine\ORM\EntityRepository
{

    public function loadLastProductEntry()
    {
        return $this->createQueryBuilder('p')
		    ->select('p')
		    ->orderBy('p.id', 'DESC')
		    ->setMaxResults(1)
		    ->getQuery()
		    ->getOneOrNullResult();
    }

    public function loadAllProductsFromThisUser($user)
    {
        return $this->createQueryBuilder('p')
		    ->select('p')
		    ->where('p.user = :user AND p.deleted = 0')
		    ->setParameter('user', $user)
		    ->orderBy('p.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }

    public function loadAllDeletedProductsFromThisUser($user)
    {
        return $this->createQueryBuilder('p')
		    ->select('p')
		    ->where('p.user = :user AND p.deleted = 1')
		    ->setParameter('user', $user)
		    ->orderBy('p.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }

    public function loadAllProductsFromThisCategory($category)
    {
        return $this->createQueryBuilder('p')
		    ->select('p')
		    ->where('p.category = :category AND p.deleted = 0')
		    ->setParameter('category', $category)
		    ->orderBy('p.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }

    public function searchMatchingProducts($searchText)
    {
    	return $this->createQueryBuilder('p')
               ->where('p.productName LIKE :input OR p.productCode LIKE :input')
               ->setParameter('input', '%'.$searchText.'%')
               ->setMaxResults(10)
               ->getQuery()
               ->getResult();
    }

}