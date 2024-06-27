<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QuestionModel;
use CodeIgniter\HTTP\ResponseInterface;

class QuestionController extends BaseController
{
    public function __construct()
    {
        echo view('main');
    }

    public function search()
    {
        $model = new QuestionModel();

        $questions = $model->getQuestionsGroupedBySousCategorie();

        // Vérifier si des données sont disponibles
        if (!empty($questions)) {
            echo view('header');
            echo view('newDiagnostic', ['questions' => $questions]);
        } else {
            echo "Aucune question trouvée.";
        }
    }
}
