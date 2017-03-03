<?php

namespace Church\Controller;

use Church\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route(service="church.controller_csrf")
 */
class CsrfController extends Controller
{

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        SerializerInterface $serializer,
        RegistryInterface $doctrine,
        TokenStorageInterface $tokenStorage,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        parent::__construct($serializer, $doctrine, $tokenStorage);
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route("/token.{_format}",
     *  defaults= {
     *    "_format" = "json"
     *  }
     *)
     * @Method("GET")
     *
     * @param Request $request
     */
    public function showAction(Request $request) : Response
    {
        return $this->serializer->serialize($this->csrfTokenManager->getToken(self::CSRF_TOKEN_ID), $request->getRequestFormat());
    }
}
