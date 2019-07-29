<?php

namespace NotesGalleryApp\Models;

class Note
{
    /**
     * Note id
     *
     * @var string
     */
    public $id;

    /**
     * Note title
     *
     * @var string
     */
    public $title;

    /**
     * Note description
     *
     * @var string|null
     */
    public $description;

    /**
     * Note content
     *
     * @var string
     */
    public $content;

    /**
     * Note author id
     *
     * @var string
     */
    public $authorId;
}
