<?php

namespace Church\MakeItHappenBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;


class Donate
{
    
    /**
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    protected $name;
    
    /**
     * @Assert\Email()
     * @Assert\MaxLength(255)
     */
    protected $email;
    
    /**
     * Amount
     *
     * @Assert\Type(type="numeric")
     */
    protected $amount;
    
    /**
     * Note
     */
    protected $note;
    
    /**
     * Stripe Token
     *
     * @Assert\NotBlank(message="Stripe Token Missing")
     */
    protected $stripe_token;
    
    
    public function setAttending($attending)
    {
        $this->attending = $attending;
    }

    public function getAttending()
    {
        return $this->attending;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }
    
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getAmount()
    {
        return $this->amount;
    }
    
    public function setNote($note)
    {
        $this->note = $note;
    }

    public function getNote()
    {
        return $this->note;
    }
    
    public function setStripeToken($stripe_token)
    {
        $this->stripe_token = $stripe_token;
    }

    public function getStripeToken()
    {
        return $this->stripe_token;
    }
    
}