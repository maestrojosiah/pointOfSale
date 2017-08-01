<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SystSetting
 *
 * @ORM\Table(name="syst_setting")
 * @ORM\Entity
 */
class SystSetting
{
    /**
     * @var string
     *
     * @ORM\Column(name="site_name", type="string", length=511, nullable=false)
     */
    private $siteName;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=30, nullable=false)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_code", type="string", length=10, nullable=false)
     */
    private $currencyCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="def_discount", type="integer", nullable=false)
     */
    private $defDiscount;

    /**
     * @var integer
     *
     * @ORM\Column(name="def_tax_rate", type="integer", nullable=false)
     */
    private $defTaxRate;

    /**
     * @var string
     *
     * @ORM\Column(name="rows_per_page", type="string", length=100, nullable=false)
     */
    private $rowsPerPage;

    /**
     * @var string
     *
     * @ORM\Column(name="default_category", type="string", length=100, nullable=false)
     */
    private $defaultCategory;

    /**
     * @var string
     *
     * @ORM\Column(name="receipt_header", type="text", length=65535, nullable=false)
     */
    private $receiptHeader;

    /**
     * @var string
     *
     * @ORM\Column(name="receipt_footer", type="text", length=65535, nullable=false)
     */
    private $receiptFooter;

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="string", length=110, nullable=false)
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\Column(name="orig_id", type="integer", nullable=false)
     */
    private $origId;

    /**
     * @var integer
     *
     * @ORM\Column(name="min_stock", type="integer", nullable=false)
     */
    private $minStock;

    /**
     * @var integer
     *
     * @ORM\Column(name="upload_duration", type="integer", nullable=false)
     */
    private $uploadDuration;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}

