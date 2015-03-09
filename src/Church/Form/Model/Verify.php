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
    protected $code;

    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }
}
