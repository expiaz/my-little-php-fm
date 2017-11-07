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
 * @var $error bool is there an error on the form
 */
/**
 * @var $message string the error message if there's one
 */
?>

<?= $renderer->render('@layout/header') ?>

<?php if($error): ?>
    <div class="error">
        <?= $message ?>
    </div>
<?php endif; ?>

<form action="<?= $router->build('@user/auth') ?>" method="POST">
    <label>
        Login :<br/>
        <input type="text" name="login" placeholder="Xxmarcdu42xX"/><br/>
    </label>
    <label>
        Mot de passe :<br/>
        <input type="password" name="password"/><br/>
    </label>
    <input type="submit" name="Valider"/>
</form>

<?= $renderer->render('@layout/footer') ?>
