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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="bankings")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;



    /**
     * Set onDate
     *
     * @param \DateTime $onDate
     *
     * @return Banking
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
     * Set transaction
     *
     * @param string $transaction
     *
     * @return Banking
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;

        return $this;
    }

    /**
     * Get transaction
     *
     * @return string
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return Banking
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set balance
     *
     * @param integer $balance
     *
     * @return Banking
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get balance
     *
     * @return integer
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set origId
     *
     * @param integer $origId
     *
     * @return Banking
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
     * @return Banking
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
