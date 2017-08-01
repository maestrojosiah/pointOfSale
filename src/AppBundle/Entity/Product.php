<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product", indexes={@ORM\Index(name="product_code", columns={"product_code"}), @ORM\Index(name="product_name", columns={"product_name"})})
 * @ORM\Entity
 */
class Product
{
    /**
     * @var integer
     *
     * @ORM\Column(name="orig_id", type="integer", nullable=false)
     */
    private $origId;

    /**
     * @var integer
	 *
     * @ORM\Column(name="category_id", type="integer", nullable=false)
     */
    private $categoryId;

    /**
     * @var string
     *
     * @ORM\Column(name="product_code", type="string", length=100, nullable=false)
     */
    private $productCode;

    /**
     * @var string
     *
     * @ORM\Column(name="product_name", type="string", length=200, nullable=false)
     */
    private $productName;

    /**
     * @var string
     *
     * @ORM\Column(name="product_cost", type="string", length=100, nullable=false)
     */
    private $productCost;

    /**
     * @var string
     *
     * @ORM\Column(name="product_tax", type="string", length=100, nullable=false)
     */
    private $productTax;

    /**
     * @var string
     *
     * @ORM\Column(name="product_retail_price", type="string", length=100, nullable=false)
     */
    private $productRetailPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="string", length=110, nullable=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="deleted", type="integer", nullable=false)
     */
    private $deleted;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
	
	/**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
	 * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
	 */
	private $category;


}

