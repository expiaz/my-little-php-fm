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
 * @var $image \App\Module\Image\Model\Entity\Image the current image
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
        <a href="<?= $router->build('image.jump', ['forward' => 0, 'image' => $image->getId()]) ?>">Prev</a>
        <a href="<?= $router->build('image.jump', ['forward' => 1, 'image' => $image->getId()]) ?>">Next</a>
        <img src="<?= $image->getURL(); ?>"/>
    </p>

<?= $renderer->render('@layout/footer') ?>