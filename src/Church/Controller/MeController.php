<?php

namespace Church\Controller;

use Church\Entity\Location;
use Church\Entity\User\User;
use Church\Entity\User\Login;
use Church\Entity\User\Email;
use Church\Entity\User\Verify\EmailVerify;
use Church\Utils\User\VerificationManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Current User actions.
 *
 * @Route(
 *    service="church.controller_me",
 *    defaults = {
 *       "version" = "1.0",
 *       "_format" = "json"
 *    }
 * )
 */
class MeController extends Controller
{

    /**
     * @var VerificationManagerInterface
     */
    protected $verificationManager;

    /**
     * @var CsrfTokenManagerInterface
     */
    protected $csrfTokenManager;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        RegistryInterface $doctrine,
        TokenStorageInterface $tokenStorage,
        VerificationManagerInterface $verificationManager,
        CsrfTokenManagerInterface $csrfTokenManager
    ) {
        parent::__construct($serializer, $validator, $doctrine, $tokenStorage);
        $this->verificationManager = $verificationManager;
        $this->csrfTokenManager = $csrfTokenManager;
    }

   /**
    * Show the current user.
    *
    * @Route("/me.{_format}")
    * @Method("GET")
    * @Security("has_role('authenticated')")
    *
    * @param Request $request
    */
    public function showAction(Request $request) : Response
    {
        return $this->reply($this->getUser(), $request->getRequestFormat(), ['me']);
    }

    /**
     * Update the current user.
     *
     * @Route("/me")
     * @Method("PATCH")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function updateAction(Request $request) : Response
    {
        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository(User::class);
        $user = $repository->find($this->getUser()->getId());
        $user = $this->deserialize($request, $user, ['me']);

        $em->flush();

        // Refresh the user.
        $this->tokenStorage->getToken()->setAuthenticated(false);

        return $this->showAction($request);
    }

    /**
     * Show the user's real name
     *
     * @Route("/me/name.{_format}")
     * @Method("GET")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function showNameAction(Request $request) : Response
    {
        return $this->reply($this->getUser()->getName(), $request->getRequestFormat(), ['me']);
    }

    /**
     * Update the user's real name.
     *
     * @Route("/me/name")
     * @Method("PATCH")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function updateNameAction(Request $request) : Response
    {
        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository(User::class);
        $user = $repository->find($this->getUser()->getId());
        $name = $this->deserialize($request, $user->getName(), ['me']);
        $user->setName($name);

        $em->flush();

        // Refresh the user.
        $this->tokenStorage->getToken()->setAuthenticated(false);

        return $this->showNameAction($request);
    }

    /**
     * Show the ueer's emails.
     *
     * @Route("/me/emails")
     * @Method("GET")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function showEmails(Request $request) : Response
    {
        return $this->reply($this->getUser()->getEmails(), $request->getRequestFormat(), ['me']);
    }

    /**
     * Add emails to the user.
     *
     * @Route("/me/emails")
     * @Method("POST")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function createEmailAction(Request $request) : Response
    {
        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository(User::class);
        $user = $repository->find($this->getUser()->getId());
        $email = $this->deserialize($request, Email::class, ['me']);

        $email->setUser($user);
        $user->addEmail($email);
        $em->flush();

        // @TODO Send a verification.

        // Refresh the user.
        $this->tokenStorage->getToken()->setAuthenticated(false);

        return $this->reply($email, $request->getRequestFormat(), ['me'], 201);
    }

    /**
     * Shows the user's primary email.
     *
     * @Route("/me/primary-email")
     * @Method("GET")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function showPrimaryEmailAction(Request $request) : Response
    {
        return $this->reply($this->getUser()->getPrimaryEmail(), $request->getRequestFormat(), ['me']);
    }

    /**
     * Sets the user's primary email.
     *
     * @Route("/me/primary-email")
     * @Method("POST")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function setPrimaryEmailAction(Request $request) : Response
    {
        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository(User::class);
        $user = $repository->find($this->getUser()->getId());
        $input = $this->deserialize($request, Email::class, ['me']);

        $accepted = null;
        foreach ($user->getEmails() as $email) {
            if ($email->getEmail() === $input->getEmail()) {
                $accepted = $email;
            }
        }

        if (!$accepted) {
            throw new BadRequestHttpException("Can only set primary email from user's existing emails");
        }

        if (!$accepted->getVerified()) {
            throw new BadRequestHttpException("Can only set a verified email as the primary email");
        }

        $user->setPrimaryEmail($accepted);
        $em->flush();

        // Refresh the user.
        $this->tokenStorage->getToken()->setAuthenticated(false);

        return $this->showPrimaryEmailAction($request);
    }

    /**
     * Shows the user's location.
     *
     * @Route("/me/location")
     * @Method("GET")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function showLocaitonAction(Request $request) : Response
    {
        return $this->reply($this->getUser()->getLocation(), $request->getRequestFormat(), ['me']);
    }

    /**
     * Sets the user's location
     *
     * @Route("/me/location")
     * @Method("POST")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function setLocationAction(Request $request) : Response
    {
        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository(User::class);
        $user = $repository->find($this->getUser()->getId());

        $input = $this->deserialize($request, Location::class, ['me']);

        dump($input);
        exit;

        // Refresh the user.
        $this->tokenStorage->getToken()->setAuthenticated(false);

        return $this->showLocation($request);
    }

    /**
     * Removes an email.
     *
     * @Route("/me/emails/{email}")
     * @Method("DELETE")
     * @Security("has_role('authenticated')")
     *
     * @param string $email
     * @param Request $request
     */
    public function removeEmailAction(string $email, Request $request) : Response
    {
        $em = $this->doctrine->getEntityManager();
        $repository = $em->getRepository(Email::class);

        $email = $repository->find($email);
        $em->remove($email);
        $em->flush();

        // Refresh the user.
        $this->tokenStorage->getToken()->setAuthenticated(false);

        return $this->reply('', $request->getRequestFormat(), ["me"], 204);
    }

    /**
     * Login Action.
     *
     * @Route("/login")
     * @Method("POST")
     * @Security("!has_role('authenticated')")
     *
     * @param Request $request
     */
    public function loginAction(Request $request) : Response
    {
        $login = $this->deserialize($request, Login::class);

        $verification = $this->verificationManager->getVerification($login->getType());

        $verify = $verification->create($login);

        $verification->send($verify);

        return $this->reply($verify, $request->getRequestFormat());
    }

    /**
     * Verify Email.
     *
     * @Route("/verify/email", name="me_verify_email")
     * @Method("POST")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function verifyEmailAction(Request $request) : Response
    {
        $input = $this->deserialize($request, EmailVerify::class);
        $em = $this->doctrine->getEntityManager();
        $repository = $this->doctrine->getRepository(EmailVerify::class);

        if ($verify = $repository->findOneByToken($input->getToken())) {
            $email = $verify->getEmail();

            $email->setVerified(new \DateTime());

            $em->persist($email);
            $em->remove($verify);
            $em->flush();

            $this->tokenStorage->getToken()->setAuthenticated(false);
        }

        $this->csrfTokenManager->refreshToken(self::CSRF_TOKEN_ID);

        return $this->showAction($request);
    }
}
