<?php

use App\Controllers\Task;
use CodeIgniter\Config\Services;
use App\Controllers\Users;

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
//inicio de sesion y generación de token
$routes->post('/api/login', 'Login::auth');

//rutas de usuarios
$routes->post('/api/usuario', 'Users::create');
$routes->patch('/api/usuario/chage_p', 'Users::updatePassword', ['filter' => 'authmiddleware']);

$routes->post('/api/passchangereq', 'Users::passChangeReq');
$routes->patch('/api/changepasswordo/(:segment)', 'Users::changePassOutSesion/$1');

//Rutas para las tareas
$routes->post('/api/task', 'Task::create', ['filter' => 'authmiddleware']);
$routes->get('/api/task', 'Task::index', ['filter' => 'authmiddleware']);
$routes->get('/api/task/(:segment)', 'Task::show/$1', ['filter' => 'authmiddleware']);
$routes->put('/api/task/(:segment)', 'Task::update/$1', ['filter' => 'authmiddleware']);
$routes->delete('/api/task/(:segment)', 'Task::delete/$1', ['filter' => 'authmiddleware']);
