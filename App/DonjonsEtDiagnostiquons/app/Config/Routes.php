<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('newDiagnostic', 'QuestionController::search');
$routes->post('addDiagnostic', 'ReponseEntrepriseController::sauvegarderNewDiag');
