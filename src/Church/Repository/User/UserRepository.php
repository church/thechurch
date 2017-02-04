<?php

namespace Church\Repository\User;

use Church\Entity\User\Email;
use Church\Entity\User\Phone;
use Church\Entity\User\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\UnitOfWork;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserRepository extends EntityRepository implements UserProviderInterface
{

    /**
     * Create a new User from an Email.
     *
     * @param Email $email Valid email object.
     *
     * @return User Newly created user object.
     */
    public function createFromEmail(Email $email) : User
    {
        $em = $this->getEntityManager();

        // Create a new stub user.
        $user = $this->createStub();

        // Set the Email
        $email->setUser($user);
        $user->setPrimaryEmail($email);

        // Save the Email
        $em->persist($email);
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Create a new User from a Phone.
     *
     * @param Phone $phone Valid phone object.
     *
     * @return User Newly created user object.
     */
    public function createFromPhone(Phone $phone) : User
    {
        $em = $this->getEntityManager();

        // Create a new stub user.
        $user = $this->createStub();

        // Set the Phone
        $phone->setUser($user);
        $user->setPrimaryPhone($phone);

        // Save the phone.
        $em->persist($phone);
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * Create a stub User.
     *
     * @return User Newly created user object.
     */
    private function createStub() : User
    {
        $em = $this->getEntityManager();

        // Create a new stub user.
        $user = new User();

        // Save the User. Since there is a possability that the id could already
        // exist, catch the exception and try again.
        $saved = false;

        while (!$saved) {
            try {
                $user = new User();
                $em->persist($user);
                $em->flush();
                $saved = true;
            } catch (UniqueConstraintViolationException $e) {
                // Try again.
            }
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $query = $this->createQueryBuilder('u');
        $query->where('u.username = :username OR e.email = :email');
        $query->leftJoin('u.emails', 'e');
        $query->setParameter('username', $username);
        $query->setParameter('email', $username);

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $query->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin Church:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user) : UserInterface
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
            || is_subclass_of($class, $this->getEntityName());
    }
}
