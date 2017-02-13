<?php

namespace Church\Controller;

use Church\Entity\User\Login;
use Church\Entity\User\Verify\EmailVerify;
use Church\Utils\User\VerificationManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
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

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        RegistryInterface $doctrine,
        TokenStorageInterface $tokenStorage,
        VerificationManagerInterface $verificationManager
    ) {
        parent::__construct($serializer, $validator, $doctrine, $tokenStorage);
        $this->verificationManager = $verificationManager;
    }

   /**
    * Show the current user.
    *
    * @Route("/me.{_format}")
    * @Method("GET")
    *
    * @param Request $request
    */
    public function showAction(Request $request) : Response
    {
        if (!$this->isLoggedIn()) {
            throw new NotFoundHttpException('Not Logged In');
        }

        return $this->reply($this->getUser(), $request->getRequestFormat(), $this->getGroups(['me']));
    }

    /**
     * Login Action.
     *
     * @Route("/login")
     * @Method("POST")
     * @Security("!has_role('ROLE_USER')")
     *
     * @param Request $request
     */
    public function loginAction(Request $request) : Response
    {
        $login = $this->deserialize($request, Login::class);

        $verification = $this->verificationManager->getVerification($login->getType());

        $verify = $verification->create($login);

        $verification->send($verify);

        return $this->reply($verify, $request->getRequestFormat(), $this->getGroups());
    }

    /**
     * Verify Email.
     *
     * @Route("/verify/email", name="me_verify_email")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
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

        return $this->showAction($request);
    }
}
