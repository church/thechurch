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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
        TokenStorageInterface $tokenStorage,
        VerificationManagerInterface $verificationManager,
        PlaceFinderInterface $placeFinder
    ) {
        parent::__construct($serializer, $doctrine, $tokenStorage);
        $this->verificationManager = $verificationManager;
        $this->placeFinder = $placeFinder;
    }

    /**
     * @Route("/user.{_format}")
     * @Method("GET")
     *
     * @param Request $request
     */
    public function indexAction(Request $request) : Response
    {
        if (!$request->query->has('username')) {
            throw new BadRequestHttpException("Missing Username Paramter");
        }

        $repository = $this->doctrine->getRepository(User::class);

        $user = $repository->findOneByUsername($request->query->get('username'));

        if (!$user) {
            throw new NotFoundHttpException("No user found");
        }

        return $this->showAction($user, $request);
    }

  /**
   * @Route("/user/{user}.{_format}")
   * @Method("GET")
   *
   * @param User $user
   * @param Request $request
   */
    public function showAction(User $user, Request $request) : Response
    {
        if (!$user->isEnabled()) {
            throw new NotFoundHttpException("User account is disabled");
        }

        return $this->serializer->respond($user, $request->getRequestFormat(), $this->getRoles($user));
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
    public function updateAction(User $user, Request $request) : Response
    {
        if (!$this->getUser()->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $user = $this->serializer->request($request, $user, $this->getRoles($user));

        $em->flush();

        return $this->showAction($user, $request);
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
    public function showNameAction(User $user, Request $request) : Response
    {
        return $this->serializer->respond($user->getName(), $request->getRequestFormat(), $this->getRoles($user));
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
    public function updateNameAction(User $user, Request $request) : Response
    {
        if (!$this->getUser()->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $name = $this->serializer->request($request, $user->getName(), $this->getRoles($user));
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
    public function showEmails(User $user, Request $request) : Response
    {
        return $this->serializer->respond($user->getEmails(), $request->getRequestFormat(), $this->getRoles($user));
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
    public function createEmailAction(User $user, Request $request) : Response
    {
        if (!$this->getUser()->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $email = $this->serializer->request($request, Email::class, $this->getRoles($user));

        $email->setUser($user);
        $user->addEmail($email);
        $em->flush();

        $verification = $this->verificationManager->getVerification('email');

        $verify = $verification->create($email);

        $verification->send($verify);

        return $this->serializer->respond($verify, $request->getRequestFormat(), $this->getRoles($user), 201);
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
    public function showPrimaryEmailAction(User $user, Request $request) : Response
    {
        return $this->serializer->respond(
            $user->getPrimaryEmail(),
            $request->getRequestFormat(),
            $this->getRoles($user)
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
    public function setPrimaryEmailAction(User $user, Request $request) : Response
    {
        if (!$this->getUser()->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $input = $this->serializer->request($request, Email::class, $this->getRoles($user));

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
    public function showLocaitonAction(User $user, Request $request) : Response
    {
        return $this->serializer->respond($user->getLocation(), $request->getRequestFormat(), $this->getRoles($user));
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
    public function setLocationAction(User $user, Request $request) : Response
    {
        if (!$this->getUser()->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $input = $this->serializer->request($request, Location::class, $this->getRoles($user));

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
    public function removeEmailAction(User $user, Email $email, Request $request) : Response
    {
        if (!$this->getUser()->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();

        $em->remove($email);
        $em->flush();

        return $this->serializer->respond(
            '',
            $request->getRequestFormat(),
            $this->getRoles($user),
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
    public function verifyEmailAction(User $user, Request $request) : Response
    {
        if (!$this->getUser()->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $input = $this->serializer->request($request, EmailVerify::class);
        $em = $this->doctrine->getEntityManager();
        $repository = $this->doctrine->getRepository(EmailVerify::class);

        $verify = $repository->findOneByToken($input->getToken());
        if (!$verify) {
            throw new BadRequestHttpException("Token does not exist'");
        }

        if (!$this->getUser()->isEqualTo($verify->getEmail()->getUser())) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $created = clone $verify->getCreated();
        $created->add(new \DateInterval('PT1H'));

        $now = new \DateTime('now');

        if ($created < $now) {
            throw new BadRequestHttpException('Verification Code is older than 1 hour');
        }

        if ($verify->isEqualTo($input)) {
            throw new BadRequestHttpException('Token & Verification Code do not match');
        }

        $email = $verify->getEmail();

        $email->setVerified(new \DateTime());

        $em->persist($email);
        $em->remove($verify);
        $em->flush();

        $this->tokenStorage->getToken()->setAuthenticated(false);

        return $this->showEmails($user, $request);
    }

    /**
     * Get Roles.
     */
    protected function getRoles(User $user) : array
    {
        $roles = [];
        if ($this->getUser()) {
            if ($this->getUser()->isEqualTo($user)) {
                $roles[] = 'me';
            } elseif ($this->getUser()->isNeighbor($user)) {
                $roles[] = 'neighbor';
            }
        }

        return $roles;
    }
}
