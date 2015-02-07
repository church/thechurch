<?php

namespace Church\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Verify
{

    /**
     * @Assert\Length(
     *      min = 6,
     *      max = 6
     * )
     * @Assert\Type(type="numeric")
     */
    protected $token;

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getToken()
    {
        return $this->token;
    }

}
