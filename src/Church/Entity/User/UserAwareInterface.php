<?php

namespace Church\Entity\User;

interface UserAwareInterface
{
    /**
     * Get user
     *
     * @return User
     */
    public function getUser() :? User;
}
