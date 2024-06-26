<?php

namespace App\Models;

use CodeIgniter\Model;

class EntrepriseModel extends Model {

    protected $table = 'Entreprise'; // Nom de la table des entreprises
    protected $primaryKey = 'Id'; // Clé primaire de la table des entreprises

    protected $allowedFields = ['Nom'];

    // Fonction pour récupérer toutes les entreprises
    public function getAllEntreprises() {
        $builder = $this->db->table('Entreprise');
        $builder->select('Id, Nom');
        $query = $builder->get();
        return $query->getResultArray();
    }
}