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

    <div>
        <nav>
            <a href="<?= $router->build('category.image.jump', [
                'category' => $category->getId(),
                'forward' => 0,
                'image' => $image->getId(),
            ]) ?>">Prev</a>
            <a href="<?= $router->build('category.image.jump', [
                'category' => $category->getId(),
                'forward' => 1,
                'image' => $image->getId(),
            ]) ?>">Next</a>
        </nav>
        <img src="<?= $image->getURL(); ?>"/>
        <blockquote>
            <?= $image->getDescription() ?>
        </blockquote>
        <p>Catégorie :
            <?php if($image->getCategory() !== null): ?>
                <a href="<?= $router->build('category.show', ['category' => $image->getCategory()->getId()]) ?>">
                    <?= $image->getCategory()->getName() ?>
                </a>
            <?php else: ?>
                Aucune
            <?php endif;?>
        </p>
        <p>Auteur :
            <?php if($image->getAuthor() !== null): ?>
                <a href="<?= $router->build('user.show', ['user' => $image->getAuthor()->getId()]) ?>">
                    <?= $image->getAuthor()->getName() ?>
                </a>
            <?php else: ?>
                Aucun
            <?php endif;?>
        </p>
        <!--<p>
            <i class="fa fa-up"></i> <?/*= $image->getUpvotes() */?> Upvotes <br/>
            <i class="fa fa-down"></i> <?/*= $image->getDownvotes() */?> Downvotes <br/>
        </p>-->
    </div>
    <!--<section>
        <ul>
            <?php /*foreach ($image->getComments() as $comment): */?>

            <?php /*endforeach; */?>
        </ul>
    </section>-->

<?= $renderer->render('@layout/footer') ?>