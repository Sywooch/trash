<?php $ratingFull = round($rating, 0, PHP_ROUND_HALF_DOWN);

$ratingEmpty = 5 - $ratingFull;
?>
<ul>
    <?php for ($i = 0; $i < $ratingFull; $i++) { ?>
        <li class="full"></li>
    <?php } ?>
    <?php for ($i = 0; $i < $ratingEmpty; $i++) { ?>
        <li class="empty"></li>
    <?php } ?>
</ul>