<?php

namespace Kuma\BroodjesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DefaultOrder
 *
 * @ORM\Table(name="kuma_broodjesbundle_default_orders")
 * @ORM\Entity
 */
class DefaultOrder extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="day", type="integer", nullable=true)
     */
    private $day;

    /**
     * @var integer
     *
     * @ORM\Column(name="pause", type="integer", nullable=true)
     */
    private $pause;

    /**
     * @ORM\ManyToOne(targetEntity="Kuma\BroodjesBundle\Entity\EndProduct",inversedBy="defaults", fetch="EAGER")
     * @ORM\JoinColumn(name="endProduct_id", referencedColumnName="id", nullable=true)
     */
    private $endProduct;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\AdminBundle\Entity\User", fetch="EAGER")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * Set day
     *
     * @param integer $day
     *
     * @return DefaultOrder
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return integer
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set pause
     *
     * @param integer $pause
     *
     * @return DefaultOrder
     */
    public function setPause($pause)
    {
        $this->pause = $pause;

        return $this;
    }

    /**
     * Get pause
     *
     * @return integer
     */
    public function getPause()
    {
        return $this->pause;
    }

    /**
     * Set endProduct
     *
     * @param \Kuma\BroodjesBundle\Entity\EndProduct $endProduct
     *
     * @return DefaultOrder
     */
    public function setEndProduct(\Kuma\BroodjesBundle\Entity\EndProduct $endProduct = null)
    {
        $this->endProduct = $endProduct;

        return $this;
    }

    /**
     * Get endProduct
     *
     * @return \Kuma\BroodjesBundle\Entity\EndProduct
     */
    public function getEndProduct()
    {
        return $this->endProduct;
    }

    /**
     * Set user
     *
     * @param \Kunstmaan\AdminBundle\Entity\User $user
     *
     * @return DefaultOrder
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
}
