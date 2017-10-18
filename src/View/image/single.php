<p>
    <a href="/image/prev/<?php echo $img->getId(); ?>/<?php echo $size; ?>">Prev</a>
    <a href="/image/next/<?php echo $img->getId(); ?>/<?php echo $size; ?>">Next</a>
    <a href="/image/zoom/1/<?php echo $img->getId(); ?>/<?php echo $size; ?>">
        <img src="<?php echo $img->getURL(); ?>" width="<?php echo $size; ?>" />
    </a>
</p>