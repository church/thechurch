<?php

namespace Church\Controller;

use Church\Entity\User\Login;
use Church\Entity\User\Verify\EmailVerify;
use Church\Utils\User\VerificationManagerInterface;
use Church\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManagerInterface;
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
 *    service="church.controller_auth",
 *    defaults = {
 *       "version" = "1.0",
 *       "_format" = "json"
 *    }
 * )
 */
class AuthController extends Controller
{

    /**
     * @var VerificationManagerInterface
     */
    protected $verificationManager;

    /**
     * @var JWTManagerInterface
     */
    protected $jwtManager;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        SerializerInterface $serializer,
        RegistryInterface $doctrine,
        VerificationManagerInterface $verificationManager,
        JWTManagerInterface $jwtManager,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($serializer, $doctrine, $tokenStorage);
        $this->verificationManager = $verificationManager;
        $this->jwtManager = $jwtManager;
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
        $login = $this->serializer->request($request, Login::class);

        $verification = $this->verificationManager->getVerification($login->getType());

        $verify = $verification->create($login);

        $verification->send($verify);

        return $this->serializer->respond($verify, $request->getRequestFormat());
    }

    /**
     * Verify Email.
     *
     * @Route("/login/email")
     * @Method("POST")
     * @Security("!has_role('authenticated')")
     *
     * @param Request $request
     */
    public function loginEmailAction(Request $request) : Response
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

        return $this->serializer->respond([
            'token' => $this->jwtManager->create($email->getUser()),
        ], $request->getRequestFormat());
    }

    /**
     * Exchange current token for a new one.
     *
     * @Route("/token")
     * @Method("GET")
     * @Security("has_role('authenticated')")
     *
     * @param Request $request
     */
    public function tokenAction(Request $request) : Response
    {
        return new Response();
    }
}
