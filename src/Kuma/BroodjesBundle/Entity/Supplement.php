<?php

namespace Kuma\BroodjesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ProductVariation
 *
 * @ORM\Table(name="kuma_broodjesbundle_supplement")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity
 */
class Supplement extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=4, scale=2)
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @ORM\ManyToMany(targetEntity="Kuma\BroodjesBundle\Entity\Category", inversedBy="supplements")
     * @ORM\JoinTable(name="kuma_supplement_category")
     * @Assert\NotBlank()
     */
    private $categories;

    /**
     * @var
     *
     * @ORM\ManyToMany(targetEntity="Kuma\BroodjesBundle\Entity\EndProduct", mappedBy="supplements")
     */
    private $endProducts;

    /**
     * @var \DateTime $deletedAt
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        $this->endProducts = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Supplement
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
     * Set price
     *
     * @param string $price
     *
     * @return Supplement
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
     * Add endProduct
     *
     * @param \Kuma\BroodjesBundle\Entity\EndProduct $endProduct
     *
     * @return Supplement
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
     * Add category
     *
     * @param \Kuma\BroodjesBundle\Entity\Category $category
     *
     * @return Supplement
     */
    public function addCategory(\Kuma\BroodjesBundle\Entity\Category $category)
    {
        $this->categories[] = $category;

        return $this;
    }

    /**
     * Remove category
     *
     * @param \Kuma\BroodjesBundle\Entity\Category $category
     */
    public function removeCategory(\Kuma\BroodjesBundle\Entity\Category $category)
    {
        $this->categories->removeElement($category);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return Supplement
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
