<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Duplicate
 *
 * @ORM\Table(name="duplicate")
 * @ORM\Entity
 */
class Duplicate
{
    /**
     * @var integer
     *
     * @ORM\Column(name="product_id", type="integer", nullable=false)
     */
    private $productId;

    /**
     * @var integer
     *
     * @ORM\Column(name="to_category_id", type="integer", nullable=false)
     */
    private $toCategoryId;

    /**
     * @var string
     *
     * @ORM\Column(name="category_id", type="string", length=100, nullable=false)
     */
    private $categoryId;

    /**
     * @var integer
     *
     * @ORM\Column(name="orig_id", type="integer", nullable=false)
     */
    private $origId;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="duplicates")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;



    /**
     * Set productId
     *
     * @param integer $productId
     *
     * @return Duplicate
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * Get productId
     *
     * @return integer
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * Set toCategoryId
     *
     * @param integer $toCategoryId
     *
     * @return Duplicate
     */
    public function setToCategoryId($toCategoryId)
    {
        $this->toCategoryId = $toCategoryId;

        return $this;
    }

    /**
     * Get toCategoryId
     *
     * @return integer
     */
    public function getToCategoryId()
    {
        return $this->toCategoryId;
    }

    /**
     * Set categoryId
     *
     * @param string $categoryId
     *
     * @return Duplicate
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set origId
     *
     * @param integer $origId
     *
     * @return Duplicate
     */
    public function setOrigId($origId)
    {
        $this->origId = $origId;

        return $this;
    }

    /**
     * Get origId
     *
     * @return integer
     */
    public function getOrigId()
    {
        return $this->origId;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Duplicate
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
