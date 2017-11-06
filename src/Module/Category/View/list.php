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
 * @var $categories \App\Module\Category\Model\Entity\Category[] the categories to display
 */
/**
 * @var $category \App\Module\Category\Model\Entity\Category
 */
?>
<?= $renderer->render('@layout/header') ?>

<h2>Catégories</h2>
<ul>
    <?php foreach ($categories as $category): ?>
        <li>
            <a href="<?=$router->build('category.show', [
                'category' => $category->getId()
                ])?>">
                <?=$category->getName()?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>

<?= $renderer->render('@layout/footer') ?>