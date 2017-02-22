<?php

namespace Kuma\BroodjesBundle\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * IntroTextPagePart
 *
 * @ORM\Table(name="kuma_broodjesbundle_intro_text_page_parts")
 * @ORM\Entity
 */
class IntroTextPagePart extends AbstractPagePart
{
    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=false)
     * @Assert\NotBlank()
     */
    private $content;

    /**
     * @param string $content
     *
     * @return IntroTextPagePart
     */
    public function setContent($content)
    {
	$this->content = $content;

	return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
	return $this->content;
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return 'KumaBroodjesBundle:PageParts:IntroTextPagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \Kuma\BroodjesBundle\Form\PageParts\IntroTextPagePartAdminType
     */
    public function getDefaultAdminType()
    {
	return new \Kuma\BroodjesBundle\Form\PageParts\IntroTextPagePartAdminType();
    }
}
