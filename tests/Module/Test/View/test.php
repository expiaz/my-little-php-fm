<?php
/**
 * @var $renderer \App\Core\Renderer
 */
/**
 * @var $router \App\Core\Http\Router\Router
 */
/**
 * @var $context \App\Core\Utils\Context
 */
?>

<h1>Test</h1>
<?= $name ?>
<?= $opt ?>

<?= $renderer->render('@test/include', [
    'name' => 'context2'
]) ?>
