<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'HomeController::index');
$routes->get('newDiagnostic', 'QuestionController::search');
$routes->post('addDiagnostic', 'ReponseEntrepriseController::sauvegarderNewDiag');
$routes->get('viewDiagnostic/(:num)', 'ReponseEntrepriseController::getReponsesEntreprise/$1');