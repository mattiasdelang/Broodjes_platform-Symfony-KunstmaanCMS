<?php

namespace Kuma\BroodjesBundle\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;

/**
 * ToTopPagePart
 *
 * @ORM\Table(name="kuma_broodjesbundle_to_top_page_parts")
 * @ORM\Entity
 */
class ToTopPagePart extends AbstractPagePart
{
    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return 'KumaBroodjesBundle:PageParts:ToTopPagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \Kuma\BroodjesBundle\Form\PageParts\ToTopPagePartAdminType
     */
    public function getDefaultAdminType()
    {
	return new \Kuma\BroodjesBundle\Form\PageParts\ToTopPagePartAdminType();
    }
}
