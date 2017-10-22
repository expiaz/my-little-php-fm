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

<h1> Information !</h1>
<p> Cette application a pour but de mettre en pratique le modèle MVC en PHP par l'équipe de SIL3 de l'IUT2 de
    Grenoble. </p>
</p> Cette version utilise les images sur le disque</p>

<?= $renderer->render('@layout/footer') ?>