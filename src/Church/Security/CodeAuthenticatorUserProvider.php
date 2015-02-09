<?php

namespace Church\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Bridge\Doctrine\RegistryInterface as Doctrine;

class CodeAuthenticatorUserProvider implements UserProviderInterface
{

  protected $doctrine;

  public function __construct(Doctrine $doctrine)
  {
    $this->doctrine = $doctrine;
  }

  /**
   * @see UserProviderInterface::loadUserByUsername()
   */
  public function loadUserByUsername($username)
  {
    $repository = $this->getDoctrine()->getRepository('Church:User\User');

    return $repository->loadUserByUsername($username);
  }

  /**
   * @see UserProviderInterface::refreshUser()
   */
  public function refreshUser(UserInterface $user)
  {
      $repository = $this->getDoctrine()->getRepository('Church:User\User');

      return $repository->refreshUser($user);
  }

  /**
   * @see UserProviderInterface::supportsClass()
   */
  public function supportsClass($class)
  {
      return 'Church\Entity\User\User' === $class;
  }

  public function getDoctrine()
  {
    return $this->doctrine;
  }

}
