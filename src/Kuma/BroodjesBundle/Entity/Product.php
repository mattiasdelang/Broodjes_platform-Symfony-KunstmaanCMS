<?php

namespace Kuma\BroodjesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Product
 *
 * @ORM\Table(name="kuma_broodjesbundle_product")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @ORM\Entity
 */
class Product extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="decimal", precision=4, scale=2)
     * @Assert\NotBlank()
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="ingredients", type="string", nullable=true)
     */
    private $ingredients;

    /**
     * @var
     *
     * @ORM\OneToMany(targetEntity="Kuma\BroodjesBundle\Entity\EndProduct", mappedBy="product")
     */
    private $endProducts;

    /**
     * @ORM\ManyToOne(targetEntity="Kuma\BroodjesBundle\Entity\Category",inversedBy="products", fetch="EAGER")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @var \DateTime $deletedAt
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    public function __construct()
    {
        $this->endProducts = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
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
     * @return Product
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
     * @return Product
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
     * Set category
     *
     * @param \Kuma\BroodjesBundle\Entity\Category $category
     *
     * @return Product
     */
    public function setCategory(\Kuma\BroodjesBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Kuma\BroodjesBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set ingredients
     *
     * @param string $ingredients
     *
     * @return Product
     */
    public function setIngredients($ingredients)
    {
        $this->ingredients = $ingredients;

        return $this;
    }

    /**
     * Get ingredients
     *
     * @return string
     */
    public function getIngredients()
    {
        return $this->ingredients;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return Product
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

    public function getCategoryName()
    {
        if ($this->getCategory()->getName()) {
            return $this->getCategory()->getName();
        }

        return null;

    }
}
