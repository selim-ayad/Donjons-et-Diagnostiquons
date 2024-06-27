<h1>Liste des diagnostics des entreprises</h1>
<ul style="list-style-type:none;">
    <?php foreach ($entreprises as $entreprise): ?>
        <li>
            <a href="<?php echo site_url('viewDiagnostic/' . $entreprise['Id']); ?>">
                <?php echo $entreprise['Nom']; ?>
            </a>
            
            <!-- Bouton pour supprimer le diagnostic -->
            <button class="btn btn-danger btn-delete-diagnostic" data-entreprise-id="<?php echo $entreprise['Id']; ?>">Supprimer</button>
        </li>
    <?php endforeach; ?>
</ul>

<!-- Message de l'api -->
<div id="message"></div>

<!-- Bouton pour créer un diagnostic -->
<?php echo anchor('newDiagnostic', 'Ajouter un diagnostic', ['class' => 'btn btn-primary addDiagBtn']); ?>
 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Script JavaScript pour gérer la suppression de diagnostic -->
<script>
    $(document).ready(function() {
        // Sélectionner tous les boutons de suppression de diagnostic
        $('.btn-delete-diagnostic').on('click', function() {
            var entrepriseId = $(this).data('entreprise-id');

            // Confirmer avant de supprimer
            if (confirm('Êtes-vous sûr de vouloir supprimer ce diagnostic?')) {
                // Appel à l'API pour supprimer le diagnostic
                $.ajax({
                    url: '<?php echo site_url("deleteDiagnostic"); ?>/' + entrepriseId,
                    type: 'DELETE',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#message').removeClass().addClass(response.status).text(response.message);

                            // Redirection si l'opération a réussi
                            if (response.status === 'success') {
                                setTimeout(function() {
                                    window.location.href = '<?= site_url('/') ?>'; // Redirection vers la page d'accueil
                                }, 3000); // Attendez 3 secondes avant de rediriger
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Une erreur est survenue.';
                        $('#message').removeClass().addClass('error').text(errorMessage);
                    }
                });
            }
        });
    });
</script>