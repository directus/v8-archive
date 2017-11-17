<?php

namespace Directus\Authentication\User;

interface UserInterface
{
    /**
     * Gets an attribute information
     *
     * @param $attribute
     *
     * @return mixed
     */
    public function get($attribute);

    /**
     * Gets the user identification number
     *
     * @return int
     */
    public function getId();

    /**
     * Gets the user email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Array representation of the object
     *
     * @return array
     */
    public function toArray();
}

