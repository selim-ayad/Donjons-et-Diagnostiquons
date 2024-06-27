<h1>Diagnostic de l'entreprise <?php echo $nomEntreprise; ?></h1>

<?php if (!empty($questions)): ?>
    <div class="containerDiag">
        <div class="tab-container">
            <!-- Onglets pour les catégories de questions -->
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
                            <!-- On créer les différents sous-catégories avec les questions rattachées -->
                            <?php foreach ($category['subCategories'] as $subCategory): ?>
                                <?php $rowspan = count($subCategory['questions']); ?>
                                <?php foreach ($subCategory['questions'] as $index => $question): ?>
                                    <tr>
                                        <?php if ($index == 0): ?>
                                            <td rowspan="<?php echo $rowspan; ?>"><?php echo $subCategory['subCategoryLabel']; ?></td>
                                        <?php endif; ?>
                                        <td><?php echo $question['intitule']; ?></td>
                                        <td>
                                            <p name="questions[<?php echo $question['id']; ?>][score]" data-category="<?php echo $categoryId; ?>" data-sub-category="<?php echo $subCategory['subCategoryId']; ?>"><?php echo $question['reponse']['score']; ?></p>
                                        </td>
                                        <td class="<?php echo $question['reponse']['score'] === '2' ? 'highlight' : ''; ?>"><?php echo $question['reponse2']; ?></td>
                                        <td class="<?php echo $question['reponse']['score'] === '1' ? 'highlight' : ''; ?>"><?php echo $question['reponse1']; ?></td>
                                        <td class="<?php echo $question['reponse']['score'] === '0' ? 'highlight' : ''; ?>"><?php echo $question['reponse0']; ?></td>
                                        <td><?php echo $question['reponse']['justification']; ?></td>
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

            
            <div id="tab4" class="tab-content">
                <p>La synthèse de toutes les infos précédentes</p>
                <?php foreach ($questions as $categoryId => $category): ?>
                    <p><?php echo $category['categoryLabel']; ?>: <span id="synthesis-average-<?php echo $categoryId; ?>">0.00</span>/5</p>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

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
        var categoryIds = new Set();
        var categoryInputs = document.querySelectorAll('p[name*="[score]"]');

        categoryInputs.forEach(function(input) {
            categoryIds.add(input.getAttribute('data-category'));
        });

        categoryIds.forEach(function(categoryId) {
            calculateAverage(categoryId);

            // Calcul de la moyenne de la catégorie pour la synthèse
            var subCategoryIds = new Set();
            var subCategoryInputs = document.querySelectorAll('p[name*="[score]"][data-category="' + categoryId + '"]');

            subCategoryInputs.forEach(function(input) {
                subCategoryIds.add(input.getAttribute('data-sub-category'));
            });
            
            subCategoryIds.forEach(function(subCategoryId) {
                calculateAverageSub(categoryId, subCategoryId);
            });
        });

        function calculateAverageSub(categoryId, subCategoryId) {
            // Calcul de la moyenne de la sous-catégorie
            var inputs = document.querySelectorAll('p[name*="[score]"][data-category="' + categoryId + '"][data-sub-category="' + subCategoryId + '"]');
            var totalScore = 0;

            inputs.forEach(function(input) {
                if (input.innerText) {
                    totalScore += parseInt(input.innerText);
                }
            });

            var averageScore = inputs.length > 0 ? (totalScore / (inputs.length)) : 0;
            var averageElement = document.getElementById('sub-category-average-' + subCategoryId);

            if (averageElement) {
                averageElement.innerText = averageScore.toFixed(2);
            } else {
                console.error('Element not found: #sub-category-average-' + subCategoryId);
            }
        }

        function calculateAverage(categoryId) {
            // Calcul de la moyenne de la catégorie pour la synthèse
            var subCategoryIds = new Set();
            var subCategoryInputs = document.querySelectorAll('p[name*="[score]"][data-category="' + categoryId + '"]');

            subCategoryInputs.forEach(function(input) {
                subCategoryIds.add(input.getAttribute('data-sub-category'));
            });

            var totalSubCategoryScore = 0;
            var subCategoryCount = 0;

            subCategoryIds.forEach(function(subCategoryId) {
                var inputsFromSubCat = document.querySelectorAll('p[name*="[score]"][data-category="' + categoryId + '"][data-sub-category="' + subCategoryId + '"]');
                var totalScoreSub = 0;

                inputsFromSubCat.forEach(function(input) {
                    if (input.innerText) {
                        totalScoreSub += parseInt(input.innerText);
                    }
                });

                var averageScoreSub = inputsFromSubCat.length > 0 ? (totalScoreSub / (inputsFromSubCat.length)) : 0;
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

<?php else: ?>
    <p>Aucune question trouvée.</p>
<?php endif; ?>
