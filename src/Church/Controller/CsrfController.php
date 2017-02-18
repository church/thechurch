<?php

namespace Church\Controller;

use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(service="church.controller_csrf")
 */
class CsrfController extends Controller
{
    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        RegistryInterface $doctrine,
        TokenStorageInterface $tokenStorage,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        parent::__construct($serializer, $validator, $doctrine, $tokenStorage);
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
        return $this->reply($this->csrfTokenManager->getToken(self::CSRF_TOKEN_ID), $request->getRequestFormat());
    }
}
