<form id="diagnosticForm" class="newDiag">
    <div class="tab-container">
        <!-- On créer les différents onglets -->
        <div class="tabs">
            <?php $tabIndex = 1; ?>
            <?php foreach ($questions as $categoryId => $category): ?>
                <button type="button" class="tab-link <?php echo $tabIndex === 1 ? 'active' : ''; ?>" data-tab="tab<?php echo $tabIndex; ?>"><?php echo $category['categoryLabel']; ?></button>
                <?php $tabIndex++; ?>
            <?php endforeach; ?>
            <button type="button" class="tab-link" data-tab="tab4">Synthèse</button>
        </div>

        <?php $tabIndex = 1; ?>
        <?php foreach ($questions as $categoryId => $category): ?>
            <div id="tab<?php echo $tabIndex; ?>" class="tab-content <?php echo $tabIndex === 1 ? 'active' : ''; ?>">
                <table>
                    <thead>
                        <tr>
                            <th>Items</th>
                            <th>Questionnements</th>
                            <th>Score</th>
                            <th>2 points</th>
                            <th>1 point</th>
                            <th>0 point</th>
                            <th>Commentaires</th>
                            <th>Démarche pour progresser</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- On créer les différents sous-catégories avec les questions rattachés -->
                        <?php foreach ($category['subCategories'] as $subCategory): ?>
                            <?php $rowspan = count($subCategory['questions']); ?>
                            <?php foreach ($subCategory['questions'] as $index => $question): ?>
                                <tr>
                                    <?php if ($index == 0): ?>
                                        <td rowspan="<?php echo $rowspan; ?>"><?php echo $subCategory['subCategoryLabel']; ?></td>
                                    <?php endif; ?>
                                    <td><?php echo $question['intitule']; ?></td>
                                    <td>
                                        <input type="hidden" name="questions[<?php echo $question['id']; ?>][id]" value="<?php echo $question['id']; ?>">
                                        <input type="radio" name="questions[<?php echo $question['id']; ?>][score]" value="0"> 0
                                        <input type="radio" name="questions[<?php echo $question['id']; ?>][score]" value="1"> 1
                                        <input type="radio" name="questions[<?php echo $question['id']; ?>][score]" value="2"> 2
                                    </td>
                                    <td><?php echo $question['reponse2']; ?></td>
                                    <td><?php echo $question['reponse1']; ?></td>
                                    <td><?php echo $question['reponse0']; ?></td>
                                    <td><textarea name="questions[<?php echo $question['id']; ?>][justification]"></textarea></td>
                                    <?php if ($index == 0): ?>
                                        <td rowspan="<?php echo $rowspan; ?>">
                                            <?php echo $subCategory['categoryDescription']; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php $tabIndex++; ?>
        <?php endforeach; ?>

        
        <div id="tab4" class="tab-content">
            <p>La synthèse de toutes les infos précédentes</p>
            <label for="entreprise">Veuillez entrez le nom de votre entreprise</label>
            <input type="text" id="entreprise" name="nomEntreprise" required />
            <button type="submit" class="btn btn-primary">Sauvegarder les réponses</button>
        </div>
    </div>
</form>

<div id="message"></div>

<!-- Permet de switch entre les différents onglets -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var tabs = document.querySelectorAll('.tab-link');
    var contents = document.querySelectorAll('.tab-content');

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            var tabId = this.getAttribute('data-tab');

            tabs.forEach(function(t) {
                t.classList.remove('active');
            });

            contents.forEach(function(content) {
                content.classList.remove('active');
            });

            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
});
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $('#diagnosticForm').on('submit', function(event) {
        event.preventDefault();

        $.ajax({
            url: '<?= site_url('addDiagnostic') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                $('#message').removeClass().addClass(response.status).text(response.message);

                // Redirection si l'opération a réussi
                if (response.status === 'success') {
                    setTimeout(function() {
                        window.location.href = '<?= site_url('/') ?>'; // Redirection vers la page d'accueil
                    }, 3000); // Attendez 3 secondes avant de rediriger
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Une erreur est survenue.';
                $('#message').removeClass().addClass('error').text(errorMessage);
            }
        });
    });
</script>