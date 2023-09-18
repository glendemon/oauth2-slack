<?php

namespace Chadhutchins\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * Class SlackResourceOwner
 *
 * @author Adam Paterson <hello@adampaterson.co.uk>
 * @author Chad Hutchins <hutchins.chad@gmail.com>
 *
 * @package Chadhutchins\OAuth2\Client\Provider
 */
class SlackResourceOwner implements ResourceOwnerInterface
{

    /**
     * @var array
     */
    protected $response;

    /**
     * SlackResourceOwner constructor.
     *
     * @param array $response
     */
    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->response;
    }

    /**
     * Get user id
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->response['https://slack.com/user_id'] ?: null;
    }

    /**
     * Get team id
     *
     * @return string|null
     */
    public function getTeamId()
    {
        return $this->response['https://slack.com/team_id'] ?: null;
    }

    /**
     * Get user email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->response['email'] ?: null;
    }

    /**
     * Is email verified?
     *
     * @return bool|null
     */
    public function isEmailVerified()
    {
        return $this->response['email_verified'] ?: null;
    }

    /**
     * Get timestamp with date of email verification
     *
     * @return int|null
     */
    public function getDateEmailVerified()
    {
        return $this->response['date_email_verified'] ?: null;
    }

    /**
     * Get user name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->response['name'] ?: null;
    }

    /**
     * Get url to user's picture
     *
     * @return string|null
     */
    public function getPicture()
    {
        return $this->response['picture'] ?: null;
    }

    /**
     * Get user given name
     *
     * @return string|null
     */
    public function getGivenName()
    {
        return $this->response['given_name'] ?: null;
    }

    /**
     * Get user family name
     *
     * @return string|null
     */
    public function getFamilyName()
    {
        return $this->response['family_name'] ?: null;
    }

    /**
     * Get user locale
     *
     * @return string|null
     */
    public function getLocale()
    {
        return $this->response['locale'] ?: null;
    }

    /**
     * Get team name
     *
     * @return string|null
     */
    public function getTeamName()
    {
        return $this->response['https://slack.com/team_name'] ?: null;
    }

    /**
     * Get team domain
     *
     * @return string|null
     */
    public function getTeamDomain()
    {
        return $this->response['https://slack.com/team_domain'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getUserImage24px()
    {
        return $this->response['https://slack.com/user_image_24'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getUserImage32px()
    {
        return $this->response['https://slack.com/user_image_32'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getUserImage48px()
    {
        return $this->response['https://slack.com/user_image_48'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getUserImage72px()
    {
        return $this->response['https://slack.com/user_image_72'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getUserImage192px()
    {
        return $this->response['https://slack.com/user_image_192'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getUserImage512px()
    {
        return $this->response['https://slack.com/user_image_512'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getUserImage1024px()
    {
        return $this->response['https://slack.com/user_image_1024'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getTeamImage34px()
    {
        return $this->response['https://slack.com/team_image_34'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getTeamImage44px()
    {
        return $this->response['https://slack.com/team_image_44'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getTeamImage68px()
    {
        return $this->response['https://slack.com/team_image_68'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getTeamImage88px()
    {
        return $this->response['https://slack.com/team_image_88'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getTeamImage102px()
    {
        return $this->response['https://slack.com/team_image_102'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getTeamImage132px()
    {
        return $this->response['https://slack.com/team_image_132'] ?: null;
    }

    /**
     * @return string|null
     */
    public function getTeamImage230px()
    {
        return $this->response['https://slack.com/team_image_230'] ?: null;
    }

    /**
     * @return bool|null
     */
    public function isTeamImageDefault()
    {
        return $this->response['https://slack.com/team_image_default'] ?: null;
    }
}
