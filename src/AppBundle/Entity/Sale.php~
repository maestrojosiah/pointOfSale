<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Sale
 *
 * @ORM\Table(name="sale")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SaleRepository")
 */
class Sale
{
    /**
     * @var integer
     *
     * @ORM\Column(name="orig_id", type="integer", nullable=false)
     */
    private $origId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="on_date", type="datetime", nullable=false)
     */
    private $onDate;

    /**
     * @var string
     *
     * @ORM\Column(name="customer", type="string", length=100, nullable=false)
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="tax", type="string", length=100, nullable=false)
     */
    private $tax;

    /**
     * @var string
     *
     * @ORM\Column(name="discount", type="string", length=100, nullable=false)
     */
    private $discount;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_mode", type="string", length=50, nullable=false)
     */
    private $paymentMode;

    /**
     * @var integer
     *
     * @ORM\Column(name="qty", type="integer", nullable=false)
     */
    private $qty;

    /**
     * @var string
     *
     * @ORM\Column(name="total_sale", type="string", length=200, nullable=false)
     */
    private $totalSale;

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sales")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Stock", mappedBy="sale")
     */
    private $stocks;
    
    public function __construct()
    {
        $this->stocks = new ArrayCollection();
        $this->deleted = false;
    }


    /**
     * Set origId
     *
     * @param integer $origId
     *
     * @return Sale
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
     * Set onDate
     *
     * @param \DateTime $onDate
     *
     * @return Sale
     */
    public function setOnDate($onDate)
    {
        $this->onDate = $onDate;

        return $this;
    }

    /**
     * Get onDate
     *
     * @return \DateTime
     */
    public function getOnDate()
    {
        return $this->onDate;
    }

    /**
     * Set customer
     *
     * @param string $customer
     *
     * @return Sale
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return string
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set tax
     *
     * @param string $tax
     *
     * @return Sale
     */
    public function setTax($tax)
    {
        $this->tax = $tax;

        return $this;
    }

    /**
     * Get tax
     *
     * @return string
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Set discount
     *
     * @param string $discount
     *
     * @return Sale
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount
     *
     * @return string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set paymentMode
     *
     * @param string $paymentMode
     *
     * @return Sale
     */
    public function setPaymentMode($paymentMode)
    {
        $this->paymentMode = $paymentMode;

        return $this;
    }

    /**
     * Get paymentMode
     *
     * @return string
     */
    public function getPaymentMode()
    {
        return $this->paymentMode;
    }

    /**
     * Set qty
     *
     * @param integer $qty
     *
     * @return Sale
     */
    public function setQty($qty)
    {
        $this->qty = $qty;

        return $this;
    }

    /**
     * Get qty
     *
     * @return integer
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set totalSale
     *
     * @param string $totalSale
     *
     * @return Sale
     */
    public function setTotalSale($totalSale)
    {
        $this->totalSale = $totalSale;

        return $this;
    }

    /**
     * Get totalSale
     *
     * @return string
     */
    public function getTotalSale()
    {
        return $this->totalSale;
    }

    /**
     * Set deleted
     *
     * @param integer $deleted
     *
     * @return Sale
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
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Sale
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

    /**
     * Add stock
     *
     * @param \AppBundle\Entity\Stock $stock
     *
     * @return Sale
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
