<?php

use App\Controllers\Task;
use CodeIgniter\Config\Services;
use App\Controllers\Users;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */



// $routes->group('/api/usuario',function($routes){
//     include __DIR__.'/api/usuario/api.php';
// });


//Rutas login
$routes->post('/api/login','Login::auth');



//rutas de usuarios
// $routes->put('/api/usuario/cp', 'Users::updatePassword');
$routes->post('/api/usuario','Users::create');




//Rutas para las tareas
$routes->post('/api/task','Task::create');
$routes->get('/api/task','Task::index',['filter' => 'authmiddleware']);
$routes->get('/api/task/(:segment)', 'Task::show/$1');
$routes->put('/api/task/(:segment)', 'Task::update/$1',['filter' => 'authmiddleware']);
$routes->delete('/api/task/(:segment)', 'Task::delete/$1');


