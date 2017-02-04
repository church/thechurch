<?php

namespace Church\Entity\User;

use Church\Entity\EntityInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Church\Validator\Constraints as ChurchAssert;

/**
 * Login Entity.
 *
 * This entity is not persisted into the database.
 */
class Login implements EntityInterface
{

    /**
     * @var array
     */
    protected const TYPES = [
      'phone',
      'email',
    ];

    /**
     * @var string
     *
     * @Assert\Length(
     *      max = "255"
     * )
     * @ChurchAssert\Login
     */
    protected $value;

    /**
     * @var string
     *
     * @Assert\Choice(callback = "getTypes")
     */
    protected $type;

    /**
     * Creates a new Login.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        $value = $data['value'] ?? '';
        $this->value = is_string($value) ? $value : '';

        $type = $data['type'] ?? null;
        $this->type = is_string($type) && in_array($type, self::TYPES) ? $type : null;
    }

    /**
     * Returns the value of the login.
     */
    public function getValue() : string
    {
        return $this->value;
    }

    /**
     * Returns the type of the login (if there is one).
     */
    public function getType() :? string
    {
        return $this->type;
    }

    /**
     * Returns all possible types.
     */
    public function getTypes() : array
    {
        return self::TYPES;
    }

    /**
     * Determines if login is an email.
     */
    public function isEmail() : bool
    {
        return $this->type === 'email';
    }

    /**
     * Determines if login is a phone number.
     */
    public function isPhone() : bool
    {
        return $this->type === 'phone';
    }

    /**
     * Convers the login object to a string.
     */
    public function __toString() : string
    {
        return $this->value;
    }
}
