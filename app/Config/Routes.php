<?php
use CodeIgniter\Config\Services;
use App\Controllers\Users;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// $routes->get("/","Users::index");



$routes->group('/api/usuario',function($routes){
    include __DIR__.'/api/usuario/api.php';
});
