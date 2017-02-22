<?php

namespace Kuma\BroodjesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LunchOrder
 *
 * @ORM\Table(name="kuma_broodjesbundle_lunch_order")
 * @ORM\Entity(repositoryClass="Kuma\BroodjesBundle\Entity\Repository\LunchOrderRepository")
 */
class LunchOrder extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var float
     *
     * @ORM\Column(name="price", type="decimal", precision=4, scale=2)
     */
    private $price;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /** @var  boolean
     * @ORM\Column(name="status", type="boolean")
     */
    private $status = 0;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="Kuma\BroodjesBundle\Entity\EndProduct", inversedBy="lunchOrders", fetch="EAGER")
     * @ORM\JoinTable(name="kuma_endproduct_order")
     */
    private $endProducts;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\AdminBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /** @var  string
     * @ORM\Column(name="productnames", type="string", nullable=true)
     */
    private $productnames;

    public function __construct()
    {
        $this->endProducts = new ArrayCollection();
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return LunchOrder
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return LunchOrder
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Add endProduct
     *
     * @param \Kuma\BroodjesBundle\Entity\EndProduct $endProduct
     *
     * @return LunchOrder
     */
    public function addEndProduct(\Kuma\BroodjesBundle\Entity\EndProduct $endProduct)
    {
        $this->endProducts[] = $endProduct;

        return $this;
    }

    /**
     * Remove endProduct
     *
     * @param \Kuma\BroodjesBundle\Entity\EndProduct $endProduct
     */
    public function removeEndProduct(\Kuma\BroodjesBundle\Entity\EndProduct $endProduct)
    {
        $this->endProducts->removeElement($endProduct);
    }

    /**
     * Get endProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEndProducts()
    {
        return $this->endProducts;
    }

    /**
     * Set user
     *
     * @param \Kunstmaan\AdminBundle\Entity\User $user
     *
     * @return LunchOrder
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
     * Set status
     *
     * @param boolean $status
     *
     * @return LunchOrder
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set productnames
     *
     * @param string $productnames
     *
     * @return LunchOrder
     */
    public function setProductnames($productnames)
    {
        $this->productnames = $productnames;

        return $this;
    }

    /**
     * Get productnames
     *
     * @return string
     */
    public function getProductnames()
    {
        return $this->productnames;
    }
}
