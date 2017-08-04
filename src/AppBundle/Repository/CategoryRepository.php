<?php 

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
    public function loadLastCategoryEntry()
    {
        return $this->createQueryBuilder('c')
		    ->select('c')
		    ->orderBy('c.id', 'DESC')
		    ->setMaxResults(1)
		    ->getQuery()
		    ->getOneOrNullResult();
    }

    public function loadAllCategoriesFromThisUser($user)
    {
        return $this->createQueryBuilder('c')
		    ->select('c')
		    ->where('c.user = :user AND c.deleted = 0')
		    ->setParameter('user', $user)
		    ->orderBy('c.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }

    public function loadAllDeletedCategoriesFromThisUser($user)
    {
        return $this->createQueryBuilder('c')
		    ->select('c')
		    ->where('c.user = :user AND c.deleted = 1')
		    ->setParameter('user', $user)
		    ->orderBy('c.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }

}