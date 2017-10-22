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
        <a href="<?= $router->build('image.jump', ['forward' => 0, 'id' => $img->getId(), 'size' => $size]) ?>">Prev</a>
        <a href="<?= $router->build('image.jump', ['forward' => 1, 'id' => $img->getId(), 'size' => $size]) ?>">Next</a>
        <a href="<?= $router->build('image.zoom', ['zoom' => 1, 'id' => $img->getId(), 'size' => $size]) ?>">
            <img src="<?= $img->getURL(); ?>" width="<?= $size; ?>"/>
        </a>
    </p>

<?= $renderer->render('@layout/footer') ?>