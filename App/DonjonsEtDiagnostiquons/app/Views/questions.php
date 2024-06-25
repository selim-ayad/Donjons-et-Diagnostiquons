<div>
    <h2>Liste des Questions</h2>
    <ul>
        <?php foreach ($questions as $question): ?>
            <li>
                <strong><?php echo $question['Intitule']; ?></strong><br>
                Réponses:
                <ul>
                    <li><?php echo $question['Reponse0']; ?></li>
                    <li><?php echo $question['Reponse1']; ?></li>
                    <li><?php echo $question['Reponse2']; ?></li>
                </ul>
                Sous-catégorie: <?php echo $question['SousCategorieNom']; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>