<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');

$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');

$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/logout', 'Auth::logout');
$routes->get('/users', 'Users::index');
$routes->get('/dashboard', 'Dashboard::index');
$routes->get('/admingejala', 'Admin::indexGejala');
$routes->post('/admin/createGejala', 'Admin::createGejala');
$routes->post('/admin/updateGejala/(:num)', 'Admin::updateGejala/$1');
$routes->get('/admin/deleteGejala/(:num)', 'Admin::deleteGejala/$1');
$routes->get('/adminpenyakit', 'Admin::indexPenyakit');
$routes->post('/admin/createPenyakit', 'Admin::createPenyakit');
$routes->post('/admin/updatePenyakit/(:num)', 'Admin::updatePenyakit/$1');
$routes->get('/admin/deletePenyakit/(:num)', 'Admin::deletePenyakit/$1');
$routes->get('/adminusers', 'Admin::indexUsers');
$routes->get('/adminkasusgejala', 'Admin::indexKasusGejala');
$routes->get('/adminkonsultasi', 'Admin::indexKonsultasi');
$routes->get('/admin/deleteUser/(:num)', 'Admin::deleteUser/$1');
$routes->post('/admin/updateUser/(:num)', 'Admin::updateUser/$1');
