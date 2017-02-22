<?php

namespace Kuma\BroodjesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * UserInfo
 *
 * @ORM\Table(name="kuma_broodjesbundle_user_info")
 * @ORM\Entity
 */
class UserInfo extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var float
     *
     * @ORM\Column(name="credits", type="decimal", precision=5, scale=2)
     */
    private $credits = 0;

    /**
     * @ORM\OneToOne(targetEntity="Kunstmaan\AdminBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @var boolean
     *
     * @ORM\Column(name="default_toggle", type="boolean")
     */
    private $defaultToggle = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="slack_name", type="string", nullable=true)
     */
    private $slackName;

    /**
     * @var string
     *
     * @ORM\Column(name="slack_access_token", type="string", nullable=true)
     */
    private $SlackAccessToken;

    /**
     * @var string
     *
     * @ORM\Column(name="slack_id", type="string", nullable=true)
     */
    private $slackId;

    /**
     * @var string
     *
     * @ORM\Column(name="slack_team_id", type="string", nullable=true)
     */
    private $slackTeamId;

    /**
     * Set credits
     *
     * @param string $credits
     *
     * @return UserInfo
     */
    public function setCredits($credits)
    {
        $this->credits = $credits;

        return $this;
    }

    /**
     * Get credits
     *
     * @return string
     */
    public function getCredits()
    {
        return $this->credits;
    }

    /**
     * Set user
     *
     * @param \Kunstmaan\AdminBundle\Entity\User $user
     *
     * @return UserInfo
     */
    public function setUser(\Kunstmaan\AdminBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Kunstmaan\AdminBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return UserInfo
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set defaultToggle
     *
     * @param boolean $defaultToggle
     *
     * @return UserInfo
     */
    public function setDefaultToggle($defaultToggle)
    {
        $this->defaultToggle = $defaultToggle;

        return $this;
    }

    /**
     * Get defaultToggle
     *
     * @return boolean
     */
    public function getDefaultToggle()
    {
        return $this->defaultToggle;
    }

    /**
     * Set slackName
     *
     * @param string $slackName
     *
     * @return UserInfo
     */
    public function setSlackName($slackName)
    {
        $this->slackName = $slackName;

        return $this;
    }

    /**
     * Get slackName
     *
     * @return string
     */
    public function getSlackName()
    {
        return $this->slackName;
    }

    /**
     * Set slackAccessToken
     *
     * @param string $slackAccessToken
     *
     * @return UserInfo
     */
    public function setSlackAccessToken($slackAccessToken)
    {
        $this->SlackAccessToken = $slackAccessToken;

        return $this;
    }

    /**
     * Get slackAccessToken
     *
     * @return string
     */
    public function getSlackAccessToken()
    {
        return $this->SlackAccessToken;
    }

    /**
     * Set slackId
     *
     * @param string $slackId
     *
     * @return UserInfo
     */
    public function setSlackId($slackId)
    {
        $this->slackId = $slackId;

        return $this;
    }

    /**
     * Get slackId
     *
     * @return string
     */
    public function getSlackId()
    {
        return $this->slackId;
    }

    /**
     * Set slackTeamId
     *
     * @param string $slackTeamId
     *
     * @return UserInfo
     */
    public function setSlackTeamId($slackTeamId)
    {
        $this->slackTeamId = $slackTeamId;

        return $this;
    }

    /**
     * Get slackTeamId
     *
     * @return string
     */
    public function getSlackTeamId()
    {
        return $this->slackTeamId;
    }
}
