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
 * @var $nb int the number of images to display
 */
?>

<ul>
    <li><a href="<?= $router->build('site.home') ?>">Home</a></li>
    <li><a href="<?= $router->build('site.info') ?>">A propos</a></li>

    <?php if($context->get('category')): ?> <!-- category -->

        <?php if($context->get('nb') === null): ?> <!-- single -->

            <li><a href="<?= $router->build('category.image.first', [
                    'category' => $category->getId()
                ]) ?>">Première image de la catégorie</a></li>
            <li><a href="<?= $router->build('category.image.random', [
                    'category' => $category->getId()
                ]) ?>">Image au hasard dans la catégorie</a></li>
            <li><a href="<?= $router->build('category.image.grid', [
                    'category' => $category->getId(),
                    'image' => $image->getId()
                ]) ?>">Plus d'images de la catégorie</a></li>

        <?php else: ?> <!-- grid -->

            <li><a href="<?= $router->build('category.image.first', [
                    'category' => $category->getId(),
                    'nb' => $nb
                ]) ?>">Premières images de la catégorie</a></li>
            <li><a href="<?= $router->build('category.image.random', [
                    'category' => $category->getId(),
                    'image' => $image->getId(),
                    'nb' => $nb
                ]) ?>">Images au hasard dans la catégorie</a></li>
            <li><a href="<?= $router->build('category.image.grid', [
                    'category' => $category->getId(),
                    'image' => $image->getId(),
                    'nb' => $nextNb
                ]) ?>">Encore plus d'images de la catégorie</a></li>

        <?php endif; ?>

    <?php elseif($context->get('image') === null): ?> <!-- home -->

        <!--<li><a href="<?/*= $router->build('image.show') */?>">Voir Photos</a></li>
        <li><a href="<?/*= $router->build('category.list') */?>">Voir Catégories</a></li>-->

    <?php elseif($context->get('nb') === null): ?> <!-- image grid -->

        <li><a href="<?= $router->build('image.first') ?>">Première image</a></li>
        <li><a href="<?= $router->build('image.random', [
                'image' => $image->getId()
            ]) ?>">Image au hasard</a></li>
        <li><a href="<?= $router->build('image.grid', [
                'image' => $image->getId()
            ]) ?>">Plus d'images</a></li>

    <?php else: ?> <!-- image -->

        <li><a href="<?= $router->build('image.first', [
                'nb' => $nb
            ]) ?>">Premières images</a></li>
        <li><a href="<?= $router->build('image.random', [
                'image' => $image->getId(),
                'nb' => $nb
            ]) ?>">Images au hasard</a></li>
        <li><a href="<?= $router->build('image.grid', [
                'image' => $image->getId(),
                'nb' => $nextNb
            ]) ?>">Plus d'images</a></li>

    <?php endif; ?>

    <li><a href="<?= $router->build('image.show') ?>">Voir Photos</a></li>
    <li><a href="<?= $router->build('category.list') ?>">Voir Catégories</a></li>
    <li>
        <form action="<?= $router->build('category.search') ?>" method="GET">
            <input type="search" name="q" placeholder="chercher par catégorie" list="cats">
            <input type="submit" value="ok">
            <datalist id="cats">
                <option>Cat</option>
            </datalist>
        </form>
    </li>
    <?php if(\App\Core\Http\Session::getInstance()->has('user')): ?>
        <li><a href="<?= $router->build('image.add') ?>">Ajouter une image</a></li>
        <li><a href="<?= $router->build('user.deco') ?>">Déconnexion</a></li>
    <?php else: ?>
        <li><a href="<?= $router->build('user.auth') ?>">Connexion</a></li>
    <?php endif; ?>

</ul>