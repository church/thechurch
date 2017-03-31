<?php

namespace Church\Controller;

use Church\Entity\Location;
use Church\Entity\User\Name;
use Church\Entity\User\User;
use Church\Entity\User\Email;
use Church\Entity\User\Verify\EmailVerify;
use Church\Utils\ArrayUtils;
use Church\Utils\PlaceFinderInterface;
use Church\Utils\User\VerificationManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

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
        DenormalizerInterface $denormalizer,
        RegistryInterface $doctrine,
        VerificationManagerInterface $verificationManager,
        PlaceFinderInterface $placeFinder
    ) {
        parent::__construct($denormalizer, $doctrine);
        $this->verificationManager = $verificationManager;
        $this->placeFinder = $placeFinder;
    }

    /**
     * @Route("/user.{_format}")
     * @Method("GET")
     *
     * @param Request $request
     */
    public function indexAction(Request $request) : User
    {
        if (!$request->query->has('username')) {
            throw new BadRequestHttpException("Missing Username Paramter");
        }

        $repository = $this->doctrine->getRepository(User::class);

        $user = $repository->findOneByUsername($request->query->get('username'));

        if (!$user) {
            throw new NotFoundHttpException("No user found");
        }

        return $user;
    }

  /**
   * @Route("/user/{user}.{_format}")
   * @Method("GET")
   *
   * @param User $user
   * @param Request $request
   */
    public function showAction(User $user) : User
    {
        if (!$user->isEnabled()) {
            throw new NotFoundHttpException("User account is disabled");
        }

        return $user;
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
    public function updateAction(User $authenticated, User $user, array $input) : User
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $user = $this->denormalizer->denormalize($input, $user);

        $em->flush();

        return $user;
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
    public function showNameAction(User $user) : Name
    {
        return $user->getName();
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
    public function updateNameAction(User $authenticated, User $user, array $input) : Name
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $name = $this->denormalizer->denormalize($input, $user->getName());
        $user->setName($name);

        $em->flush();

        return $user->getName();
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
    public function showEmails(User $user) : ArrayCollection
    {
        return $user->getEmails();
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
    public function createEmailAction(User $authenticated, User $user, array $input) : EmailVerify
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $email = $this->denormalizer->denormalize($input, Email::class);

        $email->setUser($user);
        $user->addEmail($email);
        $em->flush();

        $verification = $this->verificationManager->getVerification('email');

        $verify = $verification->create($email);

        $verification->send($verify);

        return $verify;
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
    public function showPrimaryEmailAction(User $user) : Email
    {
        if (!$user->getPrimaryEmail()) {
            throw new NotFoundHttpException("No primary email set");
        }

        return $user->getPrimaryEmail();
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
    public function setPrimaryEmailAction(User $authenticated, User $user, array $input) : Email
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $input = $this->denormalizer->denormalize($input, Email::class);

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

        return $user->getPrimaryEmail();
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
    public function showLocaitonAction(User $user) : Location
    {
        if (!$user->getLocation()) {
            throw new NotFoundHttpException("No location set.");
        }

        return $user->getLocation();
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
    public function setLocationAction(User $authenticated, User $user, array $input) : Location
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();
        $input = $this->denormalizer->denormalize($input, Location::class);

        $location = $this->placeFinder->find($input);

        $user->setLocation($location);
        $em->flush();

        return $user->getLocation();
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
    public function removeEmailAction(User $authenticated, User $user, Email $email) : string
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $em = $this->doctrine->getEntityManager();

        $em->remove($email);
        $em->flush();

        return '';
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
    public function verifyEmailAction(User $authenticated, User $user, array $input) : ArrayCollection
    {
        if (!$authenticated->isEqualTo($user)) {
            throw new AccessDeniedHttpException("You may only modify your own user");
        }

        $input = $this->denormalizer->denormalize($input, EmailVerify::class);
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

        return $user->getEmail();
    }
}
