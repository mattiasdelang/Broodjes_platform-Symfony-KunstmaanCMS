<?php

namespace Kuma\BroodjesBundle\Entity\Pages;

use Kuma\BroodjesBundle\Form\Pages\ContentPageAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Symfony\Component\Form\AbstractType;

/**
 * ContentPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="kuma_broodjesbundle_content_pages")
 */
class ContentPage extends AbstractPage implements HasPageTemplateInterface, SearchTypeInterface
{
    /**
     * Returns the default backend form type for this page
     *
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new ContentPageAdminType();
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array (
            array(
                'name'  => 'ContentPage',
                'class' => 'Kuma\BroodjesBundle\Entity\Pages\ContentPage'
            ),
	);
    }


    /**
     * {@inheritdoc}
     */
    public function getSearchType()
    {
    	return 'Page';
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
	    return array('KumaBroodjesBundle:contentpage');
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return 'KumaBroodjesBundle:Pages\ContentPage:view.html.twig';
    }
}
