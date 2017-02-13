<?php

namespace Church\Entity\User;

use Church\Entity\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Defines a person's name.
 *
 * @ORM\Embeddable()
 */
class Name implements EntityInterface
{
    /**
     * @var string
     *
     * @ORM\Column(name="first", type="string", length=255, nullable=true)
     * @Groups({"public", "me", "email"})
     */
    private $first;

    /**
     * @var string
     *
     * @ORM\Column(name="last", type="string", length=255, nullable=true)
     * @Groups({"public", "me", "email"})
     */
    private $last;

    /**
     * Create new User.
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $first = $data['first'] ?? null;
        $this->first = is_string($first) ? $first : null;

        $last = $data['last'] ?? null;
        $this->last = is_string($last) ? $last : null;
    }

    /**
     * Set First Name.
     *
     * @param string $first
     */
    public function setFirst(string $first) : self
    {
        $this->first = $first;

        return $this;
    }

    /**
     * Get First Name.
     */
    public function getFirst() :? string
    {
        return $this->first;
    }

    /**
     * Set Last.
     *
     * @param string $last
     */
    public function setLast(string $last) : self
    {
        $this->last = $last;

        return $this;
    }

    /**
     * Get Last Name.
     *
     * @return string
     */
    public function getLast() :? string
    {
        return $this->last;
    }
}
