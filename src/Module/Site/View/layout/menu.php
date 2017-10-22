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

<ul>
    <li><a href="<?= $router->build('site.home') ?>">Home</a></li>
    <li><a href="<?= $router->build('site.info') ?>">A propos</a></li>

    <?php if($context->get('img') === null): ?>

        <li><a href="<?= $router->build('image.show') ?>">Voir Photos</a></li>

    <?php else: ?>

        <li><a href="<?= $router->build('image.show') ?>">Première image</a></li>
        <li><a href="<?= $router->build('image.random', $context->get('nb') ? [
                'id' => $img->getId(),
                'size' => $size,
                'nb' => $nb
            ] : [
                'id' => $img->getId(),
                'size' => $size
            ]) ?>">Image au hasard</a></li>
        <li><a href="<?= $router->build('image.grid', $context->get('nextNb') ? [
                'id' => $img->getId(),
                'size' => $size,
                'nb' => $nextNb
            ] : [
                'id' => $img->getId(),
                'size' => $size
            ]) ?>">Plus d'images</a></li>
        <li><a href="<?= $router->build('image.zoom', [
                'zoom' => 1,
                'id' => $img->getId(),
                'size' => $size
            ]) ?>">Augmenter le zoom</a></li>
        <li><a href="<?= $router->build('image.zoom', [
                'zoom' => 0,
                'id' => $img->getId(),
                'size' => $size
            ]) ?>">Diminuer le zoom</a></li>

    <?php endif; ?>
</ul>