<?php

namespace Kuma\BroodjesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Category
 *
 * @ORM\Table(name="kuma_broodjesbundle_category")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity
 */
class Category extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Kuma\BroodjesBundle\Entity\Supplement", mappedBy="categories",fetch="EAGER")
     */
    private $supplements;

    /**
     * @ORM\OneToMany(targetEntity="Kuma\BroodjesBundle\Entity\Product", mappedBy="category",fetch="EAGER")
     */
    private $products;

    /**
     * @var \DateTime $deletedAt
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->supplements = new ArrayCollection();
    }

    /**
     * Add supplement
     *
     * @param \Kuma\BroodjesBundle\Entity\Category $supplement
     *
     * @return Category
     */
    public function addSupplement(\Kuma\BroodjesBundle\Entity\Category $supplement)
    {
        $this->supplements[] = $supplement;

        return $this;
    }

    /**
     * Remove supplement
     *
     * @param \Kuma\BroodjesBundle\Entity\Category $supplement
     */
    public function removeSupplement(\Kuma\BroodjesBundle\Entity\Category $supplement)
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
     * Add product
     *
     * @param \Kuma\BroodjesBundle\Entity\Product $product
     *
     * @return Category
     */
    public function addProduct(\Kuma\BroodjesBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \Kuma\BroodjesBundle\Entity\Product $product
     */
    public function removeProduct(\Kuma\BroodjesBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return Category
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
}
