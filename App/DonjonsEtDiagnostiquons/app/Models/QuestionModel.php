<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model {

    protected $table = 'Question'; // Nom de la table des questions
    protected $primaryKey = 'Id'; // Clé primaire de la table des questions

    protected $allowedFields = ['Intitule', 'Reponse0', 'Reponse1', 'Reponse2', 'SousCategorieId'];

    public function getQuestionsGroupedBySousCategorie() {
        $builder = $this->db->table('Question q');
        $builder->select('q.Id, q.Intitule, q.Reponse0, q.Reponse1, q.Reponse2, sc.Id as SousCategorieId, sc.Nom as SousCategorieNom, sc.Description as SousCategorieDescription, sc.CategorieId, c.Nom as CategorieNom');
        $builder->join('Sous_categorie sc', 'sc.Id = q.SousCategorieId');
        $builder->join('Categorie c', 'c.Id = sc.CategorieId');
        $query = $builder->get();
        $results = $query->getResultArray();

        // Format the data
        $formattedQuestions = [];
        foreach ($results as $row) {
            $categorieId = $row['CategorieId'];
            $categorieNom = $row['CategorieNom'];
            $sousCategorieId = $row['SousCategorieId'];
            $sousCategorieNom = $row['SousCategorieNom'];
            $sousCategorieDescription = $row['SousCategorieDescription'];

            if (!isset($formattedQuestions[$categorieId])) {
                $formattedQuestions[$categorieId] = [
                    'categoryId' => $categorieId,
                    'categoryLabel' => $categorieNom,
                    'subCategories' => []
                ];
            }

            if (!isset($formattedQuestions[$categorieId]['subCategories'][$sousCategorieId])) {
                $formattedQuestions[$categorieId]['subCategories'][$sousCategorieId] = [
                    'subCategoryId' => $sousCategorieId,
                    'subCategoryLabel' => $sousCategorieNom,
                    'categoryDescription' => $sousCategorieDescription,
                    'questions' => []
                ];
            }

            $formattedQuestions[$categorieId]['subCategories'][$sousCategorieId]['questions'][] = [
                'id' => $row['Id'],
                'intitule' => $row['Intitule'],
                'reponse0' => $row['Reponse0'],
                'reponse1' => $row['Reponse1'],
                'reponse2' => $row['Reponse2']
            ];
        }

        return array_values($formattedQuestions);
    }
}