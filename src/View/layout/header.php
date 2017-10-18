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
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/index/info">A propos</a></li>

        <?php if(! isset($img)): ?>
            <li><a href="/image/single">Voir Photos</a></li>
        <?php else: ?>
            <li><a href="/image/single/<?php echo $img->getId(); ?>/<?php echo $size ?? 480; ?>">Voir Photos</a></li>
            <li><a href="/image/first/<?php echo $size ?? 480; ?>">Première image</a></li>
            <li><a href="/image/random/<?php echo $size ?? 480; ?>">Image au hasard</a></li>
            <li><a href="/image/more/<?php echo $img->getId(); ?>/<?php echo $size ?? 480; ?>/<?php echo isset($nb) ? $nb * 2 : 2 ?>">Plus d'images</a></li>
            <li><a href="/image/zoom/1/<?php echo $img->getId(); ?>/<?php echo $size ?? 480; ?>">Augmenter le zoom</a></li>
            <li><a href="/image/zoom/0/<?php echo $img->getId(); ?>/<?php echo $size ?? 480; ?>">Diminuer le zoom</a></li>
        <?php endif; ?>
    </ul>
</div>
<div id="corps">