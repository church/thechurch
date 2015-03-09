<?php

namespace Church\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Faith
{

    /**
     * @Assert\True()
     */
    protected $faith;

    public function setFaith($faith)
    {
        $this->faith = $faith;

        return $this;
    }

    public function getFaith()
    {
        return $this->faith;
    }
}
