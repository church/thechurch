<?php

namespace Church\Entity\User\Verify;

use Doctrine\ORM\Mapping as ORM;

use Church\Entity\User\User;
use Church\Entity\User\UserAwareInterface;
use Church\Entity\User\Email;

/**
 * Church\Entity\User\EmailVerify
 *
 * @ORM\Table(name="users_email_verify")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class EmailVerify extends Verify implements UserAwareInterface
{

    /**
     * @var Email
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="\Church\Entity\User\Email", inversedBy="verify")
     * @ORM\JoinColumn(name="email", referencedColumnName="email")
     */
    private $email;

    /**
     * Create new Email Verify.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $email = $data['email'] ?? null;
        $this->email = $this->getSingle($email, Email::class);

        parent::__construct($data);
    }

    /**
     * Set email
     *
     * @param Email $email
     */
    public function setEmail(Email $email) : self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     */
    public function getEmail() :? Email
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser() :? User
    {
        if ($this->email) {
            return $this->email->getUser();
        }

        return null;
    }
}
