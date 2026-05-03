<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Login::index');
$routes->get('/users', 'Users::index');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/admindiagnosa', 'Admin::indexDiagnosa');
$routes->get('/adminpenyakit', 'Admin::indexPenyakit');