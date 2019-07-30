<?php
declare(strict_types=1);

namespace NotesGalleryApp\Views;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;

class TwigBuilder
{
    private $loader;
    private $twig;

    public function __construct()
    {
        // Specify our Twig templates location
        $this->loader = new FilesystemLoader(__DIR__);

        // instantiate Twig
        $this->twig = new Environment($this->loader, [
            'debug' => true
        ]);
        $this->twig->addExtension(new DebugExtension());

        // add global cookie and session variable access to twig template
        $this->twig->addGlobal('cookie', $_COOKIE);
        $this->twig->addGlobal('session', $_SESSION);
    }

    public function getTwig()
    {
        return $this->twig;
    }
}
