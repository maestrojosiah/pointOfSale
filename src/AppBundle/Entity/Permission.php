<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permission
 *
 * @ORM\Table(name="permission")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PermissionRepository")
 */
class Permission
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="feature", type="string", length=255)
     */
    private $feature;

    /**
     * @var integer
     *
     * @ORM\Column(name="rights", type="integer", length=2)
     */
    private $rights;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="permissions")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set feature
     *
     * @param string $feature
     *
     * @return Permission
     */
    public function setFeature($feature)
    {
        $this->feature = $feature;

        return $this;
    }

    /**
     * Get feature
     *
     * @return string
     */
    public function getFeature()
    {
        return $this->feature;
    }

    /**
     * Set rights
     *
     * @param string $rights
     *
     * @return Permission
     */
    public function setRights($rights)
    {
        $this->rights = $rights;

        return $this;
    }

    /**
     * Get rights
     *
     * @return string
     */
    public function getRights()
    {
        return $this->rights;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Permission
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
