<?php

namespace Church\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Name
{

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 255
     * )
     */
    protected $first_name;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *      max = 255
     * )
     */
    protected $last_name;

    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getFirstName()
    {
        return $this->first_name;
    }

    public function setLastName($last_name)
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getLastName()
    {
        return $this->last_name;
    }

}
