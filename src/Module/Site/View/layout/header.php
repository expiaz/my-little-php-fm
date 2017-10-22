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

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Site SIL3</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" type="text/css" href="<?=WEBROOT?>/assets/style/style.css" media="screen" title="Normal"/>
</head>
<body>
<div id="entete">
    <h1>Site SIL3</h1>
</div>
<div id="menu">
    <h3>Menu</h3>
    <?= $renderer->render('@layout/menu') ?>
</div>
<div id="corps">