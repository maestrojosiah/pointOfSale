<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product", indexes={@ORM\Index(name="product_code", columns={"product_code"}), @ORM\Index(name="product_name", columns={"product_name"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
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

	/**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="products")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $user;

    /**
     * @ORM\OneToMany(targetEntity="Stock", mappedBy="product")
     */
    private $stocks;

    public function __construct()
    {
        $this->deleted = false;
        $this->stocks = new ArrayCollection();
    }

    public function __toString() {
        return $this->productName;
    }
    
    /**
     * Set origId
     *
     * @param integer $origId
     *
     * @return Product
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
     * Set categoryId
     *
     * @param integer $categoryId
     *
     * @return Product
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;

        return $this;
    }

    /**
     * Get categoryId
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set productCode
     *
     * @param string $productCode
     *
     * @return Product
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;

        return $this;
    }

    /**
     * Get productCode
     *
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * Set productName
     *
     * @param string $productName
     *
     * @return Product
     */
    public function setProductName($productName)
    {
        $this->productName = $productName;

        return $this;
    }

    /**
     * Get productName
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->productName;
    }

    /**
     * Set productCost
     *
     * @param string $productCost
     *
     * @return Product
     */
    public function setProductCost($productCost)
    {
        $this->productCost = $productCost;

        return $this;
    }

    /**
     * Get productCost
     *
     * @return string
     */
    public function getProductCost()
    {
        return $this->productCost;
    }

    /**
     * Set productTax
     *
     * @param string $productTax
     *
     * @return Product
     */
    public function setProductTax($productTax)
    {
        $this->productTax = $productTax;

        return $this;
    }

    /**
     * Get productTax
     *
     * @return string
     */
    public function getProductTax()
    {
        return $this->productTax;
    }

    /**
     * Set productRetailPrice
     *
     * @param string $productRetailPrice
     *
     * @return Product
     */
    public function setProductRetailPrice($productRetailPrice)
    {
        $this->productRetailPrice = $productRetailPrice;

        return $this;
    }

    /**
     * Get productRetailPrice
     *
     * @return string
     */
    public function getProductRetailPrice()
    {
        return $this->productRetailPrice;
    }

    /**
     * Set deleted
     *
     * @param integer $deleted
     *
     * @return Product
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return integer
     */
    public function getDeleted()
    {
        return $this->deleted;
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
     * Set category
     *
     * @param \AppBundle\Entity\Category $category
     *
     * @return Product
     */
    public function setCategory(\AppBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \AppBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Product
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

    public function getProfit($bp, $sp, $qty = 1, $rate = 16){
        $vat = round($sp * ($rate / 116), 2);
        $diff = $sp - $bp;
        $profit = ($diff - $vat) * $qty;
        return round($profit, 2);
    }
    
    public function getVat($bp, $sp, $qty = 1, $rate = 16) {
        $vat = $sp * ($rate / 116) * $qty;
        return round($vat, 2);
    }

    /**
     * Add stock
     *
     * @param \AppBundle\Entity\Stock $stock
     *
     * @return Product
     */
    public function addStock(\AppBundle\Entity\Stock $stock)
    {
        $this->stocks[] = $stock;

        return $this;
    }

    /**
     * Remove stock
     *
     * @param \AppBundle\Entity\Stock $stock
     */
    public function removeStock(\AppBundle\Entity\Stock $stock)
    {
        $this->stocks->removeElement($stock);
    }

    /**
     * Get stocks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStocks()
    {
        return $this->stocks;
    }
}
