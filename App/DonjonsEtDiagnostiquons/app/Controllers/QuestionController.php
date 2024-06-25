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
    
    public function options()
    {
        return $this->response->setHeader('Allow', 'POST, PUT, DELETE')->setStatusCode(ResponseInterface::HTTP_OK);
    }

    public function search()
    {
        $model = new QuestionModel();

        $questions = $model->getQuestions();

        // Vérifier si des données sont disponibles
        if (!empty($questions)) {
            echo view('header');
            echo view('questions', ['questions' => $questions]);
        } else {
            echo "Aucune question trouvée.";
        }
    }
}
