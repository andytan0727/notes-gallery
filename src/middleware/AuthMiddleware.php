<?php

namespace NotesGalleryApp\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\EmptyResponse;
use function NotesGalleryLib\helpers\getCurrentUserId;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $_SERVER['REQUEST_URI'];

        // matching (notes/create)/update/delete route uri for user/note CUD actions
        // READ action to notes is permissible to all users by default
        if ($this->matchCUDRoute($uri)
        ) {
            // user is prevented to perform CUD actions without logging in
            // return 401 unauthorized
            // NOTE: intentionally choosing not to implement WWW-Authenticate header
            // to prevent infinite auth popup on client side
            if (!$this->isUserLoggedIn()) {
                return new EmptyResponse(401);
            }

            // user does not have enough privilege to perform CUD actions
            // return 403 forbidden
            if (!$this->isUserAuthenticated($request)) {
                return new EmptyResponse(403);
            }
        }
        return $handler->handle($request);
    }

    /**
     * Assert if route's uri contains create/update/delete which specify the CUD
     * operation to database
     *
     * @param string $uri
     * @return bool
     */
    private function matchCUDRoute(string $uri): bool
    {
        return strpos($uri, '/create') !== false
            || strpos($uri, '/update') !== false
            || strpos($uri, '/delete') !== false;
    }

    /**
     * Check if user is logged in
     *
     * @return boolean
     */
    private function isUserLoggedIn(): bool
    {
        return isset($_SESSION['LOGGED_IN']);
    }

    /**
     * Check if user logged in is valid and has the respective privileges to
     * perform CUD actions
     *
     * @param ServerRequestInterface $request
     * @return boolean
     */
    private function isUserAuthenticated(ServerRequestInterface $request): bool
    {
        $queryParams = $request->getQueryParams();

        // for notes CUD
        if (isset($queryParams['authorId'])) {
            $authorId = $queryParams['authorId'];

            return $this->authUserWithAuthorId($authorId);
        }

        // for user CUD
        if (isset($queryParams['userId'])) {
            $userId = $queryParams['userId'];

            return $this->authUserWithUserId($userId);
        }

        return false;
    }

    /**
     * Authenticate user if he/she is the author of the note
     *
     * @param string $authorId
     * @return bool
     */
    private function authUserWithAuthorId(string $authorId): bool
    {
        return $authorId === getCurrentUserId();
    }

    /**
     * Authenticate user if his/her id is same as the one logged in (saved in session)
     *
     * @param string $userId
     * @return bool
     */
    private function authUserWithUserId(string $userId): bool
    {
        return $userId === getCurrentUserId();
    }
}
