<?php

namespace App\Models;

use CodeIgniter\Model;

class ReponseEntrepriseModel extends Model
{
    protected $table = 'ReponseEntreprise'; // Nom de la table des réponses d'entreprise
    protected $primaryKey = 'Id'; // Clé primaire de la table des réponses d'entreprise

    protected $allowedFields = ['Valeur', 'IdEntrprise', 'IdQuestion', 'Justification'];

    public function getResponseById($id)
    {
        return $this->find($id);
    }
}
