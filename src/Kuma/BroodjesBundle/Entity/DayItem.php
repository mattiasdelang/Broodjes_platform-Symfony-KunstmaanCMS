<?php

namespace Kuma\BroodjesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DayItem
 *
 * @ORM\Table(name="kuma_broodjesbundle_day_items")
 * @ORM\Entity
 */
class DayItem extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="day", type="string", length=255, nullable=true)
     */
    private $day;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return DayItem
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
     * Set day
     *
     * @param string $day
     *
     * @return DayItem
     */
    public function setDay($day)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day
     *
     * @return string
     */
    public function getDay()
    {
        return $this->day;
    }

}