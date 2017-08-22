<?php 

namespace AppBundle\Repository;

class SystSettingRepository extends \Doctrine\ORM\EntityRepository
{

    public function loadLastSystSettingEntry()
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->orderBy('s.id', 'DESC')
		    ->setMaxResults(1)
		    ->getQuery()
		    ->getOneOrNullResult();
    }

    public function settingsForThisUser($user)
    {
        return $this->createQueryBuilder('s')
            ->select('s')
            ->where('s.user = :user')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function loadAllSystSettingsFromThisUser($user)
    {
        return $this->createQueryBuilder('s')
		    ->select('s')
		    ->where('s.user = :user AND s.deleted = 0')
		    ->setParameter('user', $user)
		    ->orderBy('s.id', 'ASC')
		    ->getQuery()
		    ->getResult();
    }

    public function getDefaultUploadDuration()
    {
    	return $this->createQueryBuilder('s')
    		->select('s')
    		->getQuery()
    		->getOneOrNullResult();
    }


}