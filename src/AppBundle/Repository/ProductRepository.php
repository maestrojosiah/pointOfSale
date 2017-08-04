<?php 

namespace AppBundle\Repository;

class ProductRepository extends \Doctrine\ORM\EntityRepository
{

	public function getProductsForUser($user)
	{
		$qb = $this->createQueryBuilder('j');
		$qb->leftJoin('j.user', 'u', 'with', 'u.user = :user');
		$qb->setParameter('user', $user);

		return $qb;
	}

	public function findAllFromThisUser($user)
	{
		$query = $this->getEntityManager()
			->createQuery(
				'SELECT p FROM AppBundle:Product p
				WHERE p.user = :user
				ORDER BY p.productName ASC'
			)->setParameter('user', $user);
		try{
			return $query->getResult();
		} catch (\Doctrine\ORM\NoResultException $e){
			return null;
		}

	}

}