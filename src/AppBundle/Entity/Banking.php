<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Banking
 *
 * @ORM\Table(name="banking")
 * @ORM\Entity
 */
class Banking
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="on_date", type="datetime", nullable=false)
     */
    private $onDate;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction", type="string", length=100, nullable=false)
     */
    private $transaction;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="string", length=100, nullable=false)
     */
    private $amount;

    /**
     * @var integer
     *
     * @ORM\Column(name="balance", type="integer", nullable=false)
     */
    private $balance;

    /**
     * @var string
     *
     * @ORM\Column(name="user_id", type="string", length=100, nullable=false)
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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


}

