<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sale
 *
 * @ORM\Table(name="sale")
 * @ORM\Entity
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
     * @ORM\Column(name="user_id", type="string", length=110, nullable=false)
     */
    private $userId;

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


}

