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
 * @var $category \App\Module\Category\Model\Entity\Category the current category
 */
/**
 * @var $image \App\Module\Image\Model\Entity\Image the image from which the others are displayed (begining of the list)
 */
/**
 * @var $images \App\Module\Image\Model\Entity\Image[] the images to display
 */
/**
 * @var $size int the size of the image
 */
/**
 * @var $nb int the number of images to display
 */

/**
 * @var $img \App\Module\Image\Model\Entity\Image the current iteration image
 */
?>

<?= $renderer->render('@layout/header') ?>

<a href="<?= $router->build('category.image.jump', [
    'category' => $category->getId(),
    'forward' => 0,
    'image' => $image->getId(),
    'nb' => $nb
]) ?>">Prev</a>
<a href="<?= $router->build('category.image.jump', [
    'category' => $category->getId(),
    'forward' => 1,
    'image' => $image->getId(),
    'nb' => $nb
]) ?>">Next</a>
<div class="container-grid">
    <?php foreach ($images as $img): ?>
        <a class="grid-item" href="<?= $router->build('category.image.show', [
            'category' => $category->getId(),
            'image' => $img->getId()
        ]) ?>">
            <img src="<?= $img->getURL(); ?>"/>
        </a>
    <?php endforeach; ?>
</div>

<?= $renderer->render('@layout/footer') ?>
<script src="<?=SCRIPTS?>script.js" type="text/javascript"></script>