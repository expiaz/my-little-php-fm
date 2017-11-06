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
 * @var $img \App\Module\Image\Model\Entity\Image the current image
 */
/**
 * @var $size int the size of the image
 */
/**
 * @var $nb int the number of images to display
 */
?>

<?= $renderer->render('@layout/header') ?>

    <p>
        <a href="<?= $router->build('category.image.jump', [
            'category' => $category->getId(),
            'forward' => 0,
            'image' => $img->getId(),
        ]) ?>">Prev</a>
        <a href="<?= $router->build('category.image.jump', [
            'category' => $category->getId(),
            'forward' => 1,
            'image' => $img->getId(),
        ]) ?>">Next</a>
        <img src="<?= $img->getURL(); ?>"/>
    </p>

<?= $renderer->render('@layout/footer') ?>