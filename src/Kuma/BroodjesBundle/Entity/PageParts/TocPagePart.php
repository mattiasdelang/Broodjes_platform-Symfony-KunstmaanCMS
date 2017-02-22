<?php

namespace Kuma\BroodjesBundle\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;

/**
 * TocPagePart
 *
 * @ORM\Table(name="kuma_broodjesbundle_toc_page_parts")
 * @ORM\Entity
 */
class TocPagePart extends AbstractPagePart
{
    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return 'KumaBroodjesBundle:PageParts:TocPagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \Kuma\BroodjesBundle\Form\PageParts\TocPagePartAdminType
     */
    public function getDefaultAdminType()
    {
	return new \Kuma\BroodjesBundle\Form\PageParts\TocPagePartAdminType();
    }
}
