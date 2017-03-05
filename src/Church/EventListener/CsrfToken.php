<?php

namespace Church\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Csrf\CsrfToken as Token;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * Csrf Token Listner.
 */
class CsrfToken
{

    /**
     * @var string
     */
    protected const CSRF_TOKEN_ID = 'api';

    /**
     * @var string
     */
    protected const CSRF_TOKEN_HEADER = 'X-CSRF-Token';

    /**
     * @var array
     */
    protected const SAFE_METHODS = ['HEAD', 'GET'];

    /**
     * @var \Symfony\Component\Security\Csrf\CsrfTokenManagerInterface
     */
    protected $tokenManager;

    /**
     * Creates the Csrf Token Listner.
     *
     * @param CsrfTokenManagerInterface $tokenManager
     */
    public function __construct(CsrfTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * onKernelRequest event
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event) : void
    {
        $request = $event->getRequest();

        // If the require request uses a "safe" method, there is no need to
        // check for a valid CSRF token.
        if (in_array($request->getMethod(), self::SAFE_METHODS)) {
            return;
        }

        if (!$request->headers->has(self::CSRF_TOKEN_HEADER)) {
            throw new BadRequestHttpException('Missing X-CSRF-Token Header');
        }

        $token = new Token(self::CSRF_TOKEN_ID, $request->headers->get(self::CSRF_TOKEN_HEADER));
        if (!$this->tokenManager->isTokenValid($token)) {
            throw new BadRequestHttpException('CSRF Token is not valid');
        }
    }
}
