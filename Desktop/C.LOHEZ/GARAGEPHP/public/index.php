<?php
// Affiche les erreurs directement dans la page 
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclure l'autoloader
require_once __DIR__ . '/Vendor/autoload.php';

// Import des classes 
use App\config\Config;
use App\Utils\Response; // Ensure the Response class exists in the App\Utils namespace
use App\Utils\Logger; // Ensure the Logger class exists in the App\Utils namespace
use FastRoute\Dispatcher;

// Démarrer une session ou reprend la session existant
session_start();

// Charger les variables d'environnement
Config::load();

// Définir des routes avec la bibliotheque FastRoute
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    

    $r->addRoute('GET', '/', [App\Controllers\HomeController::class, 'index']);
    $r->addRoute('GET', '/login', [App\Controllers\AuthController::class, 'showLogin']);
    $r->addRoute('POST', '/login', [App\Controllers\AuthController::class, 'login']);
    $r->addRoute('POST', '/logout', [App\Controllers\AuthController::class, 'logout']);
    $r->addRoute('GET', '/Cars', [App\Controllers\CarsController::class, 'index']);
});

// Traitement de la requête

// Récupérer la methode HTTP (GET, POST, PATCH) et l'URI(/login, /Cars/1)
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Dispatcher FastRoute
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
$response = new Response();

// Analyser le resultat du dispatching
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response->error("404 - page non trouvée", 404);
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWE:
        $response->error("405 - Méthode non autorisée", 405);    
        break;
    case FastRoute\Dispatcher::FOUND:
        [$controllerClass, $method] = $routeInfo[1];
        $vars = $routeInfo[2];
        try {
            $controller = new $controllerClass();
            call_user_func_array([$controller, $method], $vars);
        } catch (Exception $e) {
            if (Config::get('APP_DEBUG4') === 'true') {
                $response->error("Erreur 500 : " . $e->getMessage(). " dans " . $e->getFile() . ":" . $e->getLine(), 500);
            } else {
                (new App\Utils\Logger())->log('ERROR', 'Erreur Serveur :' .$e->getMessage());
                $response->error("une erreur interne est survenue .", 500);
            }
        }
        break;
}