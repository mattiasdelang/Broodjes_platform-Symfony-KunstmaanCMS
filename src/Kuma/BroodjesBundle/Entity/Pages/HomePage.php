<?php

namespace Kuma\BroodjesBundle\Entity\Pages;

use Kuma\BroodjesBundle\Form\Pages\HomePageAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HomePageInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Symfony\Component\Form\AbstractType;

/**
 * HomePage
 *
 * @ORM\Entity()
 * @ORM\Table(name="kuma_broodjesbundle_home_pages")
 */
class HomePage extends AbstractPage implements HasPageTemplateInterface, SearchTypeInterface, HomePageInterface
{
    /**
     * Returns the default backend form type for this page
     *
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new HomePageAdminType();
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array(
            array(
                'name'  => 'ContentPage',
                'class' => 'Kuma\BroodjesBundle\Entity\Pages\ContentPage'
            ),
            array(
                'name'  => 'BehatTestPage',
                'class' => 'Kuma\BroodjesBundle\Entity\Pages\BehatTestPage'
            )
        );
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
	    return array('KumaBroodjesBundle:main');
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
    	return array('KumaBroodjesBundle:homepage');
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return 'KumaBroodjesBundle:Pages\HomePage:view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchType()
    {
	    return 'Home';
    }
}
