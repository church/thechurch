<?php

namespace Church\Controller;

use Church\Entity\Location;
use Church\Entity\User\User;
use Church\Entity\User\Email;
use Church\Entity\User\Verify\EmailVerify;
use Church\Serializer\SerializerInterface;
use Church\Utils\ArrayUtils;
use Church\Utils\PlaceFinderInterface;
use Church\Utils\User\VerificationManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route(
 *    service="church.controller_user",
 *    defaults = {
 *       "version" = "1.0",
 *       "_format" = "json"
 *    }
 * )
 */
class UserController extends Controller
{

    /**
     * @var VerificationManagerInterface
     */
    protected $verificationManager;

    /**
     * @var PlaceFinderInterface
     */
    protected $placeFinder;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        SerializerInterface $serializer,
        RegistryInterface $doctrine,
        VerificationManagerInterface $verificationManager,
        PlaceFinderInterface $placeFinder
    ) {
        parent::__construct($serializer, $doctrine);
        $this->verificationManager = $verificationManager;
        $this->placeFinder = $placeFinder;
    }

    /**
     * @Route("/user.{_format}")
     * @Method("GET")
     *
     * @param Request $request
     */
    public function indexAction(Request $request, User $authenticated = null) : Response
    {
        if (!$request->query->has('username')) {
            throw new BadRequestHttpException("Missing Username Paramter");
        }

        $repository = $this->doctrine->getRepository(User::class);

        $user = $repository->findOneByUsername($request->query->get('username'));

        if (!$user) {
            throw new NotFoundHttpException("No user found");
        }

        return $this->showAction($user, $request, $authenticated);
    }

  /**
   * @Route("/user/{user}.{_format}")
   * @Method("GET")
   *
   * @param User $user
   * @param Request $request
   */
    public function showAction(User $user, Request $request, User $authenticated = null) : Response
    {
        if (!$user->isEnabled()) {
            throw new NotFoundHttpException("User account is disabled");
        }

        return $this->serializer->respond($user, $request->getRequestFormat(), $this->getRoles($authenticated, $user));
    }

    /**
     * Update the current user.
     *
     * @Route("/user/{user}")
     * @Method("PATCH")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function updateAction(User $authenticated, User $user, Request $request) : Response
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $user = $this->serializer->request($request, $user, $this->getRoles($authenticated, $user));

        $em->flush();

        return $this->showAction($user, $request, $authenticated);
    }

    /**
     * Show the user's real name
     *
     * @Route("/user/{user}/name.{_format}")
     * @Method("GET")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function showNameAction(
        User $authenticated,
        User $user,
        Request $request
    ) : Response {
        return $this->serializer->respond(
            $user->getName(),
            $request->getRequestFormat(),
            $this->getRoles($authenticated, $user)
        );
    }

    /**
     * Update the user's real name.
     *
     * @Route("/user/{user}/name")
     * @Method("PATCH")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function updateNameAction(User $authenticated, User $user, Request $request) : Response
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $name = $this->serializer->request($request, $user->getName(), $this->getRoles($authenticated, $user));
        $user->setName($name);

        $em->flush();

        return $this->showNameAction($user, $request);
    }

    /**
     * Show the ueer's emails.
     *
     * @Route("/user/{user}/emails.{_format}")
     * @Method("GET")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function showEmails(User $authenticated, User $user, Request $request) : Response
    {
        return $this->serializer->respond(
            $user->getEmails(),
            $request->getRequestFormat(),
            $this->getRoles($authenticated, $user)
        );
    }

    /**
     * Add emails to the user.
     *
     * @Route("/user/{user}/emails")
     * @Method("POST")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function createEmailAction(User $authenticated, User $user, Request $request) : Response
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $email = $this->serializer->request(
            $request,
            Email::class,
            $this->getRoles($authenticated, $user)
        );

        $email->setUser($user);
        $user->addEmail($email);
        $em->flush();

        $verification = $this->verificationManager->getVerification('email');

        $verify = $verification->create($email);

        $verification->send($verify);

        return $this->serializer->respond(
            $verify,
            $request->getRequestFormat(),
            $this->getRoles($authenticated, $user),
            201
        );
    }

    /**
     * Shows the user's primary email.
     *
     * @Route("/user/{user}/primary-email.{_format}")
     * @Method("GET")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function showPrimaryEmailAction(User $authenticcated = null, User $user, Request $request) : Response
    {
        return $this->serializer->respond(
            $user->getPrimaryEmail(),
            $request->getRequestFormat(),
            $this->getRoles($authenticated, $user)
        );
    }

    /**
     * Sets the user's primary email.
     *
     * @Route("/user/{user}/primary-email")
     * @Method("POST")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function setPrimaryEmailAction(User $authenticated, User $user, Request $request) : Response
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $input = $this->serializer->request($request, Email::class, $this->getRoles($authenticated, $user));

        $accepted = ArrayUtils::search($user->getEmails(), function ($item) use ($input) {
            return $item->getEmail() === $input->getEmail();
        });

        if (!$accepted) {
            throw new BadRequestHttpException("Can only set primary email from user's existing emails");
        }

        if (!$accepted->getVerified()) {
            throw new BadRequestHttpException("Can only set a verified email as the primary email");
        }

        $em = $this->doctrine->getEntityManager();
        $user->setPrimaryEmail($accepted);
        $em->flush();

        return $this->showPrimaryEmailAction($user, $request);
    }

    /**
     * Shows the user's location.
     *
     * @Route("/user/{user}/location.{_format}")
     * @Method("GET")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function showLocaitonAction(User $authenticated, User $user, Request $request) : Response
    {
        return $this->serializer->respond(
            $user->getLocation(),
            $request->getRequestFormat(),
            $this->getRoles($authenticated, $user)
        );
    }

    /**
     * Sets the user's location
     *
     * @Route("/user/{user}/location")
     * @Method("POST")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function setLocationAction(User $authenticated, User $user, Request $request) : Response
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $input = $this->serializer->request($request, Location::class, $this->getRoles($authenticated, $user));

        $location = $this->placeFinder->find($input);

        $user->setLocation($location);
        $em->flush();

        return $this->showLocaitonAction($user, $request);
    }

    /**
     * Removes an email.
     *
     * @Route("/user/{user}/emails/{email}")
     * @Method("DELETE")
     * @Security("has_role('authenticated')")
     *
     * @param Email $email
     * @param Request $request
     */
    public function removeEmailAction(User $authenticated, User $user, Email $email, Request $request) : Response
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();

        $em->remove($email);
        $em->flush();

        return $this->serializer->respond(
            '',
            $request->getRequestFormat(),
            $this->getRoles($authenticated, $user),
            204
        );
    }

    /**
     * Verify Email.
     *
     * @Route("/user/{user}/emails/verify")
     * @Method("POST")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function verifyEmailAction(User $authenticated, User $user, Request $request) : Response
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $input = $this->serializer->request($request, EmailVerify::class);
        $em = $this->doctrine->getEntityManager();
        $repository = $this->doctrine->getRepository(EmailVerify::class);

        $verify = $repository->findOneByToken($input->getToken());
        if (!$verify) {
            throw new BadRequestHttpException("Token does not exist'");
        }

        if (!$verify->isEqualTo($input)) {
            throw new BadRequestHttpException('Token & Verification Code do not match');
        }

        if (!$verify->isFresh()) {
            throw new BadRequestHttpException('Verification Code is older than 1 hour');
        }

        $email = $verify->getEmail();

        $email->setVerified(new \DateTime());

        $em->persist($email);
        $em->remove($verify);
        $em->flush();

        return $this->showEmails($authenticated, $user, $request);
    }

    /**
     * Get Roles.
     */
    protected function getRoles(User $authenticated = null, User $user) : array
    {
        $roles = [];

        if (!$authenticated) {
            return $roles;
        }

        if ($authenticated->isEqualTo($user)) {
            $roles[] = 'me';
        }

        if ($authenticated->isNeighbor($user)) {
            $roles[] = 'neighbor';
        }

        return $roles;
    }
}
