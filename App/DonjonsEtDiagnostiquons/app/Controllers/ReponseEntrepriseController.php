<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EntrepriseModel;
use App\Models\ReponseEntrepriseModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ReponseEntrepriseController extends BaseController
{
    public function __construct()
    {
    }

    public function sauvegarderNewDiag()
    {
        $entrepriseModel = new EntrepriseModel();
        $reponseEntrepriseModel = new ReponseEntrepriseModel();

        $data = $this->request->getPost('questions');
        $nomEntreprise = $this->request->getPost('nomEntreprise'); // Assuming you have a hidden field for entreprise ID

        // Vérifier si nomEntreprise est bien défini
        if (!$nomEntreprise) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Nom entreprise manquant.'
            ]);
        }

        // Vérifier que toutes les questions ont un score renseigné
        foreach ($data as $questionId => $questionData) {
            if (!isset($questionData['score'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Toutes les questions doivent avoir un score.'
                ]);
            }
        }

        try {
            // Créer l'entreprise et récupérer son ID
            $entrepriseModel->save(['Nom' => $nomEntreprise]);
            $idEntreprise = $entrepriseModel->insertID();

            foreach ($data as $questionId => $questionData) {
                $score = $questionData['score'];
                $justification = isset($questionData['justification']) ? $questionData['justification'] : '';

                // Save the response to the database
                $reponseEntrepriseModel->save([
                    'Valeur' => $score,
                    'IdEntrprise' => $idEntreprise,
                    'IdQuestion' => $questionId,
                    'Justification' => $justification
                ]);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Les réponses ont été sauvegardées avec succès.'
            ]);
        } catch (DatabaseException $e) {
            // Log l'erreur pour plus de détails
            log_message('error', $e->getMessage());
            return $this->response->setStatusCode(500, 'Erreur interne du serveur');
        }
    }
}
