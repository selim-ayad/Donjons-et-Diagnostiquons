<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EntrepriseModel;
use App\Models\QuestionModel;
use App\Models\ReponseEntrepriseModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class ReponseEntrepriseController extends BaseController
{
    public function getReponsesEntreprise($idEntreprise)
    {
        $reponseEntrepriseModel = new ReponseEntrepriseModel();
        $questionModel = new QuestionModel();
        $entrepriseModel = new EntrepriseModel();

        try {
            // Récupérer les réponses de l'entreprise depuis la base de données
            $reponses = $reponseEntrepriseModel->where('IdEntreprise', $idEntreprise)->findAll();

            // Récupérer les questions groupées par sous-catégorie
            $questions = $questionModel->getQuestionsGroupedBySousCategorie();

            // Récupérer le nom de l'entreprise
            $entreprise = $entrepriseModel->find($idEntreprise);
            $nomEntreprise = $entreprise['Nom']; // Assurez-vous que 'Nom' correspond au champ de nom dans votre modèle

            // Vérifier si des réponses ont été trouvées
            if (!empty($reponses)) {
                // Créer un tableau associatif de réponses par ID de question pour faciliter l'accès
                $reponsesMap = [];
                foreach ($reponses as $reponse) {
                    $reponsesMap[$reponse['IdQuestion']] = [
                        'score' => $reponse['Valeur'],
                        'justification' => $reponse['Justification']
                    ];
                }

                // Ajouter les réponses aux questions
                foreach ($questions as $categoryId => &$category) {
                    foreach ($category['subCategories'] as &$subCategory) {
                        foreach ($subCategory['questions'] as &$question) {
                            $questionId = $question['id'];
                            if (isset($reponsesMap[$questionId])) {
                                $question['reponse'] = $reponsesMap[$questionId];
                            } else {
                                $question['reponse'] = [
                                    'score' => null,
                                    'justification' => ''
                                ];
                            }
                        }
                    }
                }

                // Charger la vue viewDiagnostic avec les questions et les réponses
                echo view('main');
                echo view('header');
                echo view('viewDiagnostic', ['questions' => $questions, 'nomEntreprise' => $nomEntreprise]);

            } else {
                // Retourner un message d'erreur si aucune réponse n'est trouvée
                echo "Aucune réponse trouvée pour l'entreprise avec l'ID $idEntreprise";
            }

        } catch (\Exception $e) {
            // En cas d'erreur, retourner une réponse d'erreur avec le code 500
            echo "Erreur lors de la récupération des réponses de l'entreprise.";
        }
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
                    'IdEntreprise' => $idEntreprise,
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

    public function deleteReponsesEntreprise($idEntreprise)
    {
        $entrepriseModel = new EntrepriseModel();
        $reponseEntrepriseModel = new ReponseEntrepriseModel();

        try {
            // Vérifier si l'entreprise existe
            $entreprise = $entrepriseModel->find($idEntreprise);
            if (!$entreprise) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'L\'entreprise avec l\'ID spécifié n\'existe pas.'
                ]);
            }
            
            // Supprimer les réponses de l'entreprise
            $reponseEntrepriseModel->where('IdEntreprise', $idEntreprise)->delete();

            // Supprimer l'entreprise elle-même
            $entrepriseModel->delete($idEntreprise);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'L\'entreprise et ses réponses associées ont été supprimées avec succès.'
            ]);
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une réponse d'erreur avec le code 500
            log_message('error', 'Erreur lors de la suppression de l\'entreprise: ' . $e->getMessage());
            return $this->response->setStatusCode(500, 'Erreur interne du serveur');
        }
    }
}
