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
?>

<?= $renderer->render('@layout/header') ?>

<h1> <?= $title ?> </h1>
<p> Cette application vous permet de manipuler des photos <br/>
    Vous pouvez : naviguer, partager, classer en album </p>

<?= $renderer->render('@layout/footer') ?>