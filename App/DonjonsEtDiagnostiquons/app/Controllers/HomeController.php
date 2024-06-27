<?php

namespace App\Controllers;

use App\Models\EntrepriseModel;

class HomeController extends BaseController
{
    public function __construct()
    {
        echo view('main');
    }

    public function index()
    {
        $model = new EntrepriseModel();
        $entreprises = $model->getAllEntreprises();

        // Vérifier si des données sont disponibles
        if (!empty($entreprises)) {
            echo view('header');
            echo view('home', ['entreprises' => $entreprises]);
        } else {
            echo "Aucune entreprise trouvée.";
        }
    }
}
