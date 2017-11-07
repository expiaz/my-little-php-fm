<?php
/**
 * @var $renderer \App\Core\Renderer
 */
/**
 * @var $router \App\Core\Http\Router\Router
 */
/**
 * @var $context \App\Core\Utils\Context the current scope context (merged with parent's one)
 */

/**
 * @var $search string the string looking for
 */
/**
 * @var $results \App\Module\Category\Model\Entity\Category[] the categories to display
 */
/**
 * @var $result \App\Module\Category\Model\Entity\Category
 */
?>
<?= $renderer->render('@layout/header') ?>

<h1>Résultats de recherche pour <?= $search ?></h1>

<?php if(!count($results)): ?>
    <p>No results found</p>
    <p>Return to <a href="<?= $router->build('category.list') ?>">catégories</a> ?</p>
<?php else: ?>
    <ul>
    <?php foreach ($results as $result): ?>
        <li>
            <a href="<?= $router->build('category.show', ['category' => $result->getId()]) ?>">
                <?= $result->getName() ?>
            </a>
        </li>
    <?php endforeach; ?>
    </ul>
<?php endif; ?>


<?= $renderer->render('@layout/footer') ?>


