<?php

namespace Kuma\BroodjesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 *
 * @ORM\Table(name="kuma_broodjesbundle_transaction")
 * @ORM\Entity(repositoryClass="Kuma\BroodjesBundle\Entity\Repository\TransactionRepository")
 */
class Transaction extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="method", type="string", length=255, nullable=true)
     */
    private $method;

    /**
     * @var string
     *
     * @ORM\Column(name="order_id", type="string", length=255, nullable=true)
     */
    private $orderId;

    /**
     * @var string
     *
     * @ORM\Column(name="pay_date", type="string", length=255, nullable=true)
     */
    private $payDate;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_id", type="string", length=255, nullable=true)
     */
    private $profileId;

    /**
     * @var string
     *
     * @ORM\Column(name="mollie_transaction_id", type="string", length=255, nullable=true)
     */
    private $mollieTransactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="credits", type="decimal", precision=5, scale=2)
     */
    private $credits;

    /**
     * @var string
     * @ORM\Column(name="create_date", type="string")
     */
    private $createDate;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\AdminBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Set type
     *
     * @param string $method
     *
     * @return Transaction
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set credits
     *
     * @param string $credits
     *
     * @return Transaction
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;

        return $this;
    }

    /**
     * Get credits
     *
     * @return string
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * Set createDate
     *
     * @param string $createDate
     *
     * @return Transaction
     */
    public function setCreateDate($createDate)
    {
        $this->createDate = $createDate;

        return $this;
    }

    /**
     * Get createDate
     *
     * @return string
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }

    /**
     * Set user
     *
     * @param \Kunstmaan\AdminBundle\Entity\User $user
     *
     * @return Transaction
     */
    public function setUser(\Kunstmaan\AdminBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Kunstmaan\AdminBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set orderId
     *
     * @param string $orderId
     *
     * @return Transaction
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    /**
     * Get orderId
     *
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * Set payDate
     *
     * @param string $payDate
     *
     * @return Transaction
     */
    public function setPaidDate($payDate)
    {
        $this->payDate = $payDate;

        return $this;
    }

    /**
     * Get payDate
     *
     * @return string
     */
    public function getPaidDate()
    {
        return $this->payDate;
    }

    /**
     * Set profileId
     *
     * @param string $profileId
     *
     * @return Transaction
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * Get profileId
     *
     * @return string
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * Set mollieTransactionId
     *
     * @param string $mollieTransactionId
     *
     * @return Transaction
     */
    public function setMollieTransactionId($mollieTransactionId)
    {
        $this->mollieTransactionId = $mollieTransactionId;

        return $this;
    }

    /**
     * Get mollieTransactionId
     *
     * @return string
     */
    public function getMollieTransactionId()
    {
        return $this->mollieTransactionId;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Transaction
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
