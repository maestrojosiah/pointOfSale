<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stock
 *
 * @ORM\Table(name="stock")
 * @ORM\Entity
 */
class Stock
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="on_date", type="datetime", nullable=false)
     */
    private $onDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="integer", nullable=false)
     */
    private $itemId;

    /**
     * @var string
     *
     * @ORM\Column(name="quantity", type="string", length=100, nullable=false)
     */
    private $quantity;

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="string", length=110, nullable=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="sale_id", type="integer", nullable=false)
     */
    private $saleId;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction", type="string", length=200, nullable=false)
     */
    private $transaction;

    /**
     * @var integer
     *
     * @ORM\Column(name="orig_id", type="integer", nullable=false)
     */
    private $origId;

    /**
     * @var string
     *
     * @ORM\Column(name="unit_cost", type="string", length=110, nullable=false)
     */
    private $unitCost;

    /**
     * @var string
     *
     * @ORM\Column(name="retail_cost", type="string", length=100, nullable=false)
     */
    private $retailCost;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}

