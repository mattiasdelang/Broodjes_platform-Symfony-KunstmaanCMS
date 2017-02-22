<?php

namespace Kuma\BroodjesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EndProduct
 *
 * @ORM\Table(name="kuma_broodjesbundle_end_product")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity(repositoryClass="Kuma\BroodjesBundle\Entity\Repository\EndProductRepository")
 * @UniqueEntity(
 *     fields={"slackName", "user"},
 *     errorPath="slack_name",
 *     message="You already use this name for another product!"
 * )
 */
class EndProduct extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @ORM\Column(name="slack_name", type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private $slackName;

    /**
     * @ORM\ManyToOne(targetEntity="Kuma\BroodjesBundle\Entity\Product",inversedBy="endProducts", fetch="EAGER")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $product;

    /**
     * @ORM\ManyToMany(targetEntity="Kuma\BroodjesBundle\Entity\Supplement", inversedBy="endProducts", fetch="EAGER")
     * @ORM\JoinTable(name="kuma_product_supplement")
     */
    private $supplements;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\AdminBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="Kuma\BroodjesBundle\Entity\LunchOrder", mappedBy="endProducts")
     */
    private $lunchOrders;

    /**
     * @var string
     *
     * @ORM\Column(name="extra_info", type="string", nullable=true)
     */
    private $extraInfo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_favorite",type="boolean")
     */
    private $isFavorite = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @ORM\OneToMany(targetEntity="Kuma\BroodjesBundle\Entity\DefaultOrder", mappedBy="endProduct",fetch="EAGER")
     */
    private $defaults;

    private $price;

    public function __construct()
    {
        $this->supplements = new ArrayCollection();
        $this->lunchOrders = new ArrayCollection();
    }

    public function getPrice()
    {
        $supplements = $this->getSupplements();
        $prodPrice = $this->getProduct()->getPrice();

        $suppPrice = 0;

        //Calculate the total price of the endproduct
        foreach ($supplements as $supplement) {
            $suppPrice += $supplement->getPrice();
        }

        return $prodPrice + $suppPrice;
    }

    public function getName()
    {
        $name = $this->getProduct()->getName() . ' (';
        $supplements = $this->getSupplements();

        $i = 0;

        foreach ($supplements as $supplement) {
            ++$i;
            $name .= $supplement->getName() . ', ';
        }

        if ($i == 0) {
            $name = substr($name, 0, -2);
        } else {
            $name = substr($name, 0, -2);
            $name .= ')';
        }

        return $name;
    }

    /**
     * Set extraInfo
     *
     * @param string $extraInfo
     *
     * @return EndProduct
     */
    public function setExtraInfo($extraInfo)
    {
        $this->extraInfo = $extraInfo;

        return $this;
    }

    /**
     * Get extraInfo
     *
     * @return string
     */
    public function getExtraInfo()
    {
        return $this->extraInfo;
    }

    /**
     * Set isFavorite
     *
     * @param boolean $isFavorite
     *
     * @return EndProduct
     */
    public function setIsFavorite($isFavorite)
    {
        $this->isFavorite = $isFavorite;

        return $this;
    }

    /**
     * Get isFavorite
     *
     * @return boolean
     */
    public function isFavorite()
    {
        return $this->isFavorite;
    }

    /**
     * Set product
     *
     * @param \Kuma\BroodjesBundle\Entity\Product $product
     *
     * @return EndProduct
     */
    public function setProduct(\Kuma\BroodjesBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \Kuma\BroodjesBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Add supplement
     *
     * @param \Kuma\BroodjesBundle\Entity\Supplement $supplement
     *
     * @return EndProduct
     */
    public function addSupplement(\Kuma\BroodjesBundle\Entity\Supplement $supplement)
    {
        $this->supplements[] = $supplement;

        return $this;
    }

    /**
     * Remove supplement
     *
     * @param \Kuma\BroodjesBundle\Entity\Supplement $supplement
     */
    public function removeSupplement(\Kuma\BroodjesBundle\Entity\Supplement $supplement)
    {
        $this->supplements->removeElement($supplement);
    }

    /**
     * Get supplements
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSupplements()
    {
        return $this->supplements;
    }

    /**
     * Add lunchOrder
     *
     * @param \Kuma\BroodjesBundle\Entity\LunchOrder $lunchOrder
     *
     * @return EndProduct
     */
    public function addLunchOrder(\Kuma\BroodjesBundle\Entity\LunchOrder $lunchOrder)
    {
        $this->lunchOrders[] = $lunchOrder;

        return $this;
    }

    /**
     * Remove lunchOrder
     *
     * @param \Kuma\BroodjesBundle\Entity\LunchOrder $lunchOrder
     */
    public function removeLunchOrder(\Kuma\BroodjesBundle\Entity\LunchOrder $lunchOrder)
    {
        $this->lunchOrders->removeElement($lunchOrder);
    }

    /**
     * Get lunchOrders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLunchOrders()
    {
        return $this->lunchOrders;
    }

    /**
     * Get isFavorite
     *
     * @return boolean
     */
    public function getIsFavorite()
    {
        return $this->isFavorite;
    }

    /**
     * Get isDefault
     *
     * @return boolean
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * Set user
     *
     * @param \Kunstmaan\AdminBundle\Entity\User $user
     *
     * @return EndProduct
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
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return EndProduct
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Set slackName
     *
     * @param string $slackName
     *
     * @return EndProduct
     */
    public function setslackName($slackName)
    {
        $this->slackName = $slackName;

        return $this;
    }

    /**
     * Get slackName
     *
     * @return string
     */
    public function getslackName()
    {
        return $this->slackName;
    }

    /**
     * Add default
     *
     * @param \Kuma\BroodjesBundle\Entity\DefaultOrder $default
     *
     * @return EndProduct
     */
    public function addDefault(\Kuma\BroodjesBundle\Entity\DefaultOrder $default)
    {
        $this->defaults[] = $default;

        return $this;
    }

    /**
     * Remove default
     *
     * @param \Kuma\BroodjesBundle\Entity\DefaultOrder $default
     */
    public function removeDefault(\Kuma\BroodjesBundle\Entity\DefaultOrder $default)
    {
        $this->defaults->removeElement($default);
    }

    /**
     * Get defaults
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDefaults()
    {
        return $this->defaults;
    }
}
