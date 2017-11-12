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
 * @var $user \App\Module\User\Model\Entity\User the current user
 */
?>

<?= $renderer->render('@layout/header') ?>

    <header>
        <h1>Profil de <?= $user->getName() ?></h1>
    </header>
    <h2>Photos de <?= $user->getName() ?> :</h2>
    <div class="container-grid">
        <?php foreach ($user->getImages()->asArray() as $img): ?>
            <a class="grid-item" href="<?= $router->build('image.show', [
                'image' => $img->getId()
            ]) ?>">
                <img src="<?= $img->getURL(); ?>"/>
            </a>
        <?php endforeach; ?>
    </div>

<?= $renderer->render('@layout/footer') ?>
<script src="<?=SCRIPTS?>script.js" type="text/javascript"></script>
