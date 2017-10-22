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
 * @var $img \App\Module\Image\Model\Entity\Image the image from which the others are displayed (begining of the list)
 */
/**
 * @var $images \App\Module\Image\Model\Entity\Image[] the images to display
 */
/**
 * @var $image \App\Module\Image\Model\Entity\Image
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
    <a href="<?= $router->build('image.jump', [
        'forward' => 0,
        'id' => $img->getId(),
        'size' => $size,
        'nb' => $nb
    ]) ?>">Prev</a>
    <a href="<?= $router->build('image.jump', [
        'forward' => 1,
        'id' => $img->getId(),
        'size' => $size,
        'nb' => $nb
    ]) ?>">Next</a>


    <?php foreach ($images as $image): ?>
        <a href="<?= $router->build('image.zoom', [
            'zoom' => 1,
            'id' => $image->getId(),
            'size' => $size
        ]) ?>">
            <img src="<?= $image->getURL(); ?>" width="<?= $columnSize; ?>"/>
        </a>
    <?php endforeach; ?>
</p>

<?= $renderer->render('@layout/footer') ?>
