<?php

namespace Kuma\BroodjesBundle\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;

/**
 * LinePagePart
 *
 * @ORM\Table(name="kuma_broodjesbundle_line_page_parts")
 * @ORM\Entity
 */
class LinePagePart extends AbstractPagePart
{
    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return 'KumaBroodjesBundle:PageParts:LinePagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \Kuma\BroodjesBundle\Form\PageParts\LinePagePartAdminType
     */
    public function getDefaultAdminType()
    {
	return new \Kuma\BroodjesBundle\Form\PageParts\LinePagePartAdminType();
    }
}
