<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/artikel/(:segment)', 'Home::artikel/$1');
$routes->get('/konseling', 'Home::konseling');
$routes->post('/konseling-anak', 'Home::storeAnak');
$routes->get('/konsultasi', 'Diagnosa::index');
$routes->post('/konsultasi', 'Diagnosa::index');
$routes->get('/konsultasi/laporan/(:num)', 'Diagnosa::laporan/$1');
$routes->get('/konsultasi/laporan/download/(:num)', 'Diagnosa::downloadLaporan/$1');
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
$routes->get('/adminhipotesis', 'Admin::indexHipotesis');
$routes->get('/adminpenyakit', 'Admin::indexPenyakit');
$routes->post('/admin/createPenyakit', 'Admin::createPenyakit');
$routes->post('/admin/updatePenyakit/(:num)', 'Admin::updatePenyakit/$1');
$routes->get('/admin/deletePenyakit/(:num)', 'Admin::deletePenyakit/$1');
$routes->get('/adminusers', 'Admin::indexUsers');
$routes->get('/adminsettings', 'Admin::settings');
$routes->post('/adminsettings', 'Admin::updateSettings');
$routes->get('/adminanak', 'Admin::indexAnak');
$routes->post('/admin/updateAnak/(:num)', 'Admin::updateAnak/$1');
$routes->get('/admin/deleteAnak/(:num)', 'Admin::deleteAnak/$1');
$routes->get('/adminkasusgejala', 'Admin::indexKasusGejala');
$routes->get('/adminhasildiagnosa', 'Admin::indexHasilDiagnosa');
$routes->get('/admin/deleteHasilDiagnosa/(:num)', 'Admin::deleteHasilDiagnosa/$1');
$routes->get('/adminstatusgizi', 'Admin::indexStatusGizi');
$routes->post('/admin/uploadStatusGizi', 'Admin::uploadStatusGizi');
$routes->get('/adminstandar', 'Admin::indexStandarAntropometri');
$routes->post('/admin/updateStandar/(:num)', 'Admin::updateStandarAntropometri/$1');
$routes->get('/adminprior', 'Admin::indexTeoremaBayesPrior');
$routes->post('/admin/updatePrior/(:num)', 'Admin::updateTeoremaBayesPrior/$1');
$routes->get('/adminlikelihood', 'Admin::indexTeoremaBayesLikelihood');
$routes->post('/admin/updateLikelihood/(:num)', 'Admin::updateTeoremaBayesLikelihood/$1');
$routes->get('/adminnilaiprobabilitas', 'Admin::indexNilaiProbabilitas');
$routes->get('/adminrulebased', 'Admin::indexRuleBased');
$routes->post('/admin/createRuleBased', 'Admin::createRuleBased');
$routes->post('/admin/updateRuleBased/(:num)', 'Admin::updateRuleBased/$1');
$routes->get('/admin/deleteRuleBased/(:num)', 'Admin::deleteRuleBased/$1');
$routes->get('/admin/deleteUser/(:num)', 'Admin::deleteUser/$1');
$routes->post('/admin/createUser', 'Admin::createUser');
$routes->post('/admin/updateUser/(:num)', 'Admin::updateUser/$1');
$routes->post('/admin/createRole', 'Admin::createRole');
$routes->post('/admin/updateRoleAccess', 'Admin::updateRoleAccess');
