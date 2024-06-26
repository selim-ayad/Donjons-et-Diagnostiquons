<h1>Liste des diagnostics des entreprises</h1>
<ul>
    <?php foreach ($entreprises as $entreprise): ?>
        <li><?php echo $entreprise['Nom']; ?></li>
    <?php endforeach; ?>
</ul>

<!-- Bouton pour créer un diagnostic -->
<?php echo anchor('newDiagnostic', 'Ajouter un diagnostic', ['class' => 'btn btn-primary']); ?>