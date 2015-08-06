<?php
/**
 * @var array $menu
 */
?>

<nav class="nav">

    <ul class="nav_list">
        <?php
            $currentUrl = Yii::app()->request->getUrl();

            foreach ($menu as $item) {
                if (!empty($item['Hidden'])) continue;
                $isCurrent = $currentUrl == $item['URL'];
        ?>
            <li class="nav_list__item<?php echo $isCurrent ? ' s-current' : ''; ?>">
                <?php if ($isCurrent): ?>
                    <span class="nav_list__link">
                        <span class="nav_list__text"><?php echo $item['Title']; ?></span>
                    </span>
                <?php else: ?>
                    <a class="nav_list__link<?php echo empty($item['class']) ? '' : ' ' . $item['class']; ?>" href="<?php echo $item['URL']; ?>">
                        <span class="nav_list__text"><?php echo $item['Title']; ?></span>
                    </a>
                <?php endif; ?>
            </li>
        <?php } ?>
    </ul>

</nav>
