<p>
    <a href="/image/prev/<?php echo $img->getId(); ?>/<?php echo $size ?? 480; ?>/<?php echo $nb ?? 2; ?>">Prev</a>
    <a href="/image/next/<?php echo $img->getId(); ?>/<?php echo $size ?? 480; ?>/<?php echo $nb ?? 2; ?>">Next</a>
</p>
<?php foreach ($images as $image): ?>
    <a href="/image/single/<?php echo $image->getId(); ?>/<?php echo $size ?? 480; ?>">
        <img src="<?php echo $image->getUrl(); ?>" width="<?php echo $columnSize; ?>" height="<?php echo $columnSize ?>"/>
    </a>
<?php endforeach; ?>