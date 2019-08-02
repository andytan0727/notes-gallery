<?php

namespace NotesGalleryApp\Models;

class User
{
    /**
     * User id
     *
     * @var string
     */
    public $id;

    /**
     * User username
     *
     * @var string
     */
    public $username;

    /**
     * User password
     *
     * @var string
     */
    public $password;

    /**
     * JWT holding user's information
     *
     * @var string
     */
    public $token;

    /**
     * Url for randomly generated avatar profile picture for a user
     */
    public $avatarUrl;
}
