<?php

namespace Church\Controller;

use Church\Entity\Location;
use Church\Entity\User\User;
use Church\Entity\User\Email;
use Church\Entity\User\Verify\EmailVerify;
use Church\Utils\ArrayUtils;
use Church\Utils\PlaceFinderInterface;
use Church\Utils\User\VerificationManagerInterface;
use Church\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
        return $this->serializer->respond($this->getUser(), $request->getRequestFormat(), ['me']);
    }

    /**
     * Update the current user.
     *
     * @Route(
     *   "/me",
     *   name="me",
     * )
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
        $user = $this->serializer->request($request, $user, ['me']);

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
        return $this->serializer->respond($this->getUser()->getName(), $request->getRequestFormat(), ['me']);
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
        $name = $this->serializer->request($request, $user->getName(), ['me']);
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
        return $this->serializer->respond($this->getUser()->getEmails(), $request->getRequestFormat(), ['me']);
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
        $email = $this->serializer->request($request, Email::class, ['me']);

        $email->setUser($user);
        $user->addEmail($email);
        $em->flush();

        $verification = $this->verificationManager->getVerification('email');

        $verify = $verification->create($email);

        $verification->send($verify);

        // Refresh the user.
        $this->tokenStorage->getToken()->setAuthenticated(false);

        return $this->serializer->respond($verify, $request->getRequestFormat(), ['me'], 201);
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
        return $this->serializer->respond($this->getUser()->getPrimaryEmail(), $request->getRequestFormat(), ['me']);
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
        $input = $this->serializer->request($request, Email::class, ['me']);

        $accepted = ArrayUtils::search($user->getEmails(), function ($item) use ($input) {
            return $item->getEmail() === $input->getEmail();
        });

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
        return $this->serializer->respond($this->getUser()->getLocation(), $request->getRequestFormat(), ['me']);
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

        $input = $this->serializer->request($request, Location::class, ['me']);

        $location = $this->placeFinder->find($input);

        $user->setLocation($location);
        $em->flush();

        // Refresh the user.
        $this->tokenStorage->getToken()->setAuthenticated(false);

        return $this->showLocaitonAction($request);
    }

    /**
     * Removes an email.
     *
     * @Route("/me/emails/{email}")
     * @Method("DELETE")
     * @Security("has_role('authenticated')")
     *
     * @param Email $email
     * @param Request $request
     */
    public function removeEmailAction(Email $email, Request $request) : Response
    {
        $em = $this->doctrine->getEntityManager();

        $em->remove($email);
        $em->flush();

        // Refresh the user.
        $this->tokenStorage->getToken()->setAuthenticated(false);

        return $this->serializer->respond('', $request->getRequestFormat(), ["me"], 204);
    }

    /**
     * Verify Email.
     *
     * @Route("/me/emails/verify", name="me_verify_email")
     * @Method("POST")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function verifyEmailAction(Request $request) : Response
    {
        $input = $this->serializer->request($request, EmailVerify::class);
        $em = $this->doctrine->getEntityManager();
        $repository = $this->doctrine->getRepository(EmailVerify::class);

        $verify = $repository->findOneByToken($input->getToken());
        if (!$verify) {
            throw new BadRequestHttpException("Token does not exist'");
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

        return $this->showEmails($request);
    }
}
