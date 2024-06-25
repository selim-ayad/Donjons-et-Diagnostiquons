<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model {

    protected $table = 'Question'; // Nom de la table des questions
    protected $primaryKey = 'Id'; // Clé primaire de la table des questions

    protected $allowedFields = ['Intitule', 'Reponse0', 'Reponse1', 'Reponse2', 'SousCategorield'];

    public function getQuestions() {
        // Sélectionne toutes les questions avec leur sous-catégorie
        $builder = $this->db->table('Question q')
                           ->select('q.*, sc.Nom as SousCategorieNom')
                           ->join('Sous_categorie sc', 'sc.Id = q.SousCategorield');
        $query = $builder->get();
        return $query->getResultArray();
    }
}