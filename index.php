<?php

use App\Redirect;
use App\Views\View;

require_once 'vendor/autoload.php';
session_start();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    // Auth
    $r->addRoute('GET', '/login', ['App\Controllers\LoginController','signin']);
    $r->addRoute('POST', '/login', ['App\Controllers\LoginController','login']);
    $r->addRoute('POST', '/logout', ['App\Controllers\LoginController','logout']);
    $r->addRoute('GET', '/register', ['App\Controllers\RegisterController','signup']);
    $r->addRoute('POST', '/register', ['App\Controllers\RegisterController','register']);
    //posts index page/ all posts
    $r->addRoute('GET', '/articles', ['App\Controllers\ArticlesController','index']);
    //create a post
    $r->addRoute('GET', '/articles/create', ['App\Controllers\ArticlesController','create']);
    $r->addRoute('POST', '/articles', ['App\Controllers\ArticlesController','store']);
    //posts - each post
    $r->addRoute('GET', '/articles/{id}', ['App\Controllers\ArticlesController','show']);
    //delete
    $r->addRoute('POST', '/articles/{id}/delete', ['App\Controllers\ArticlesController','delete']);
    //edit
    $r->addRoute('POST', '/articles/{id}/update', ['App\Controllers\ArticlesController','update']);
    $r->addRoute('GET', '/articles/{id}/edit', ['App\Controllers\ArticlesController','edit']);
    //like
    $r->addRoute('POST', '/articles/{id}/like', ['App\Controllers\ArticlesController','like']);
    //comment
    $r->addRoute('POST', '/articles/{id}/comments', ['App\Controllers\CommentsController','comment']);
    //delete comment
    $r->addRoute('POST', '/articles/{articleId}/comments/{commentId}/delete', ['App\Controllers\CommentsController','delete']);
    //see all users
    $r->addRoute('GET', '/users/all', ['App\Controllers\UsersController','all']);
    //invite
    $r->addRoute('POST', '/users/{id}/invite', ['App\Controllers\InvitesController','store']);
    //see invites
    $r->addRoute('GET', '/invites', ['App\Controllers\InvitesController','index']);
    //accept invitation and add to friends
    $r->addRoute('POST', '/users/{id}/accept', ['App\Controllers\InvitesController','accept']);
    //homepage
    $r->addRoute('GET', '/homepage', ['App\Controllers\HomepageController','index']);
    //see all friends
    $r->addRoute('GET', '/friends', ['App\Controllers\FriendsController','index']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        var_dump('404 Not Found');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler =$routeInfo[1][0];
        $method = $routeInfo[1][1];
        $vars = $routeInfo[2] ?? [];

        $view = (new $handler)->$method($vars);

        $loader = new \Twig\Loader\FilesystemLoader('app/views');
        $twig = new \Twig\Environment($loader);

        if($view instanceof View)
        {
            echo $twig->render($view->getPath(), $view->getVariables());
        }

        if($view instanceof Redirect)
        {
            header('Location: ' . $view->getLocation());
            exit;
        }

        break;
}

if (isset($_SESSION['errors'])){
    unset($_SESSION['errors']);
}

if (isset($_SESSION['inputs'])){
    unset($_SESSION['inputs']);
}