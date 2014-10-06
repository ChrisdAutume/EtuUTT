<?php

namespace Etu\Core\ApiBundle\Oauth;

use Doctrine\ORM\EntityManager;
use Etu\Core\ApiBundle\Entity\OauthAccessToken;
use Etu\Core\ApiBundle\Entity\OauthAuthorizationCode;
use Etu\Core\ApiBundle\Entity\OauthClient;
use Etu\Core\ApiBundle\Oauth\GrantType\GrantTypeInterface;
use Etu\Core\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class OauthServer
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var GrantTypeInterface[]
     */
    protected $grantTypes;

    /**
     * @param EntityManager $manager
     */
    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
        $this->grantTypes = [];
    }

    /**
     * Check access for a resource.
     *
     * @param integer $clientId
     * @param string $clientSecret
     * @param string $token
     * @param string $requiredScope
     * @return ApiAccess
     */
    public function checkAccess($clientId, $clientSecret, $token, $requiredScope = null)
    {
        $access = new ApiAccess();

        $client = $this->manager->getRepository('EtuCoreApiBundle:OauthClient')->findOneBy([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
        ]);

        if (! $client) {
            $access->setIsGranted(false);
            $access->setError('invalid_client');
            $access->setErrorMessage('Client credentials are invalid');

            return $access;
        }

        $accessToken = $this->manager->getRepository('EtuCoreApiBundle:OauthAccessToken')->findOneBy([
            'client' => $client,
            'token' => $token,
        ]);

        if (! $accessToken) {
            $access->setIsGranted(false);
            $access->setError('invalid_token');
            $access->setErrorMessage('Access token is invalid');

            return $access;
        }

        if ($accessToken->getExpireAt() <= new \DateTime()) {
            $access->setIsGranted(false);
            $access->setError('expired_token');
            $access->setErrorMessage('Access token has expired');

            return $access;
        }

        if ($requiredScope && ! $accessToken->hasScope($requiredScope)) {
            $access->setIsGranted(false);
            $access->setError('unauthrorized_scope');
            $access->setErrorMessage('Access token does not have required scope');

            return $access;
        }

        $accessToken->setClient($client);

        $access->setIsGranted(true);
        $access->setToken($accessToken);

        return $access;
    }

    /**
     * @param string $grantTypeName
     * @param Request $request
     * @return bool|\Etu\Core\ApiBundle\Entity\OauthAccessToken
     */
    public function createToken($grantTypeName, Request $request)
    {
        foreach ($this->grantTypes as $grantType) {
            if ($grantType->getName() == $grantTypeName) {
                return $grantType->createToken($request);
            }
        }

        throw new \RuntimeException('Invalid grant type');
    }

    /**
     * @param string $grantTypeName
     * @param $token $request
     * @return bool|array
     */
    public function formatToken($grantTypeName, OauthAccessToken $token)
    {
        foreach ($this->grantTypes as $grantType) {
            if ($grantType->getName() == $grantTypeName) {
                return $grantType->format($token);
            }
        }

        return false;
    }

    /**
     * @param GrantTypeInterface $grantType
     */
    public function addGrantType(GrantTypeInterface $grantType)
    {
        $this->grantTypes[] = $grantType;
    }
}
