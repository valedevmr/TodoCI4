<?php

use App\Controllers\Users;
use CodeIgniter\Config\Services;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


 $routes->post("/","Users::create");
//  $routes->put("/cp","Users::patchPassword");
 $routes->put('/cp', 'Users::updatePassword');
 