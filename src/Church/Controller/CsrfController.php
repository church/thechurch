<?php

namespace Church\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route(service="church.controller_csrf")
 */
class CsrfController extends Controller
{
    /**
     * @var string
     */
    protected const CSRF_TOKEN_ID = 'api';

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        parent::__construct($serializer, $validator);
        $this->csrfTokenManager = $csrfTokenManager;
    }

    /**
     * @Route("/token.{_format}",
   *  defaults= {
     *    "_format" = "json"
     *  }
     *)
     * @Method("GET")
     */
    public function showAction(Request $request) : Response
    {
        return $this->reply($this->csrfTokenManager->getToken(self::CSRF_TOKEN_ID), $request->getRequestFormat());
    }
}
