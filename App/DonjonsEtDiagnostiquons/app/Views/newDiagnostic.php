<h1>Ajout d'un diagnostic</h1>

<form id="diagnosticForm" class="containerDiag">
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
                                        <input type="radio" name="questions[<?php echo $question['id']; ?>][score]" value="0" data-category="<?php echo $categoryId; ?>" data-sub-category="<?php echo $subCategory['subCategoryId']; ?>"> 0
                                        <input type="radio" name="questions[<?php echo $question['id']; ?>][score]" value="1" data-category="<?php echo $categoryId; ?>" data-sub-category="<?php echo $subCategory['subCategoryId']; ?>"> 1
                                        <input type="radio" name="questions[<?php echo $question['id']; ?>][score]" value="2" data-category="<?php echo $categoryId; ?>" data-sub-category="<?php echo $subCategory['subCategoryId']; ?>"> 2
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

                <!-- Affichage de la moyenne de chaque sous-catégorie -->
                <?php foreach ($category['subCategories'] as $subCategory): ?>
                    <p>Moyenne de la sous-catégorie <?php echo $subCategory['subCategoryLabel']; ?>: <span id="sub-category-average-<?php echo $subCategory['subCategoryId']; ?>">0.00</span>/2</p>
                <?php endforeach; ?>
            </div>
            <?php $tabIndex++; ?>
        <?php endforeach; ?>

        <!-- Synthèse -->
        <div id="tab4" class="tab-content">
            <p>La synthèse de toutes les infos précédentes</p>
            <?php foreach ($questions as $categoryId => $category): ?>
                <p><?php echo $category['categoryLabel']; ?>: <span id="synthesis-average-<?php echo $categoryId; ?>">0.00</span>/5</p>
            <?php endforeach; ?>
            <label for="entreprise">Veuillez entrez le nom de votre entreprise</label>
            <input type="text" id="entreprise" name="nomEntreprise" required />
            <button type="submit" class="btn btn-primary">Sauvegarder les réponses</button>
        </div>
    </div>
</form>
                  
<!-- Message de l'api -->
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

    // Calcul des scores
    var scoreInputs = document.querySelectorAll('input[type="radio"][name*="[score]"]');
    scoreInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            var categoryId = this.getAttribute('data-category');
            var subCategoryId = this.getAttribute('data-sub-category');
            calculateAverage(categoryId, subCategoryId);
        });
    });

    function calculateAverage(categoryId, subCategoryId) {
        // Calcul de la moyenne de la sous-catégorie
        var inputs = document.querySelectorAll('input[type="radio"][name*="[score]"][data-category="' + categoryId + '"][data-sub-category="' + subCategoryId + '"]');
        var totalScore = 0;

        inputs.forEach(function(input) {
            if (input.checked) {
                totalScore += parseInt(input.value);
            }
        });

        var averageScore = inputs.length > 0 ? (totalScore / (inputs.length/3)) : 0;
        var averageElement = document.getElementById('sub-category-average-' + subCategoryId);
        if (averageElement) {
            averageElement.innerText = averageScore.toFixed(2);
        } else {
            console.error('Element not found: #sub-category-average-' + subCategoryId);
        }

        // Calcul de la moyenne de la catégorie pour la synthèse
        var subCategoryIds = new Set();
        var subCategoryInputs = document.querySelectorAll('input[type="radio"][name*="[score]"][data-category="' + categoryId + '"]');
        
        subCategoryInputs.forEach(function(input) {
            subCategoryIds.add(input.getAttribute('data-sub-category'));
        });

        var totalSubCategoryScore = 0;
        var subCategoryCount = 0;

        subCategoryIds.forEach(function(subCategoryId) {

            var inputsFromSubCat = document.querySelectorAll('input[type="radio"][name*="[score]"][data-category="' + categoryId + '"][data-sub-category="' + subCategoryId + '"]');
            var totalScoreSub = 0;

            inputsFromSubCat.forEach(function(input) {
                if (input.checked) {
                    totalScoreSub += parseInt(input.value);
                }
            });

            var averageScoreSub = inputsFromSubCat.length > 0 ? (totalScoreSub / (inputsFromSubCat.length/3)) : 0;
            totalSubCategoryScore += averageScoreSub;
            subCategoryCount++;
        });

        var categoryAverage = subCategoryCount > 0 ? (totalSubCategoryScore / subCategoryCount) * 2.5 : 0;
        var categoryAverageElement = document.getElementById('synthesis-average-' + categoryId);
        if (categoryAverageElement) {
            categoryAverageElement.innerText = categoryAverage.toFixed(2);
        } else {
            console.error('Element not found for category average.');
        }
    }
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