<?php
use App\Controllers\UsersController;
require_once 'vendor/autoload.php';



$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/user', ['App\Controllers\UsersController', 'one']);
    $r->addRoute('GET', '/users', ['App\Controllers\UsersController','all']);
    $r->addRoute('GET', '/users/{id}', ['App\Controllers\UsersController','show']);
    $r->addRoute('GET', '/articles', ['App\Controllers\ArticlesController','index']);
    $r->addRoute('GET', '/articles/create', ['App\Controllers\ArticlesController','create']);
    $r->addRoute('GET', '/articles/{id}', ['App\Controllers\ArticlesController','show']);
    $r->addRoute('POST', '/articles', ['App\Controllers\ArticlesController','store']);

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

        echo $twig->render($view->getPath(), $view->getVariables());

        break;
}