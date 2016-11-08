<?php
/**
 * Created by PhpStorm.
 * User: mirel
 * Date: 25.03.2015
 * Time: 12:53
 * @var \mpf\modules\forum\components\Controller $this
 * This requires 3 variables:
 * @var string $elementsName
 * @var int $totalElements
 * @var int $visibleElements
 * @var int $totalPages
 * @var int $currentPage
 */
?>
<div class="forum-page-list">
    <span class="forum-page-list-elements">
        <?= $visibleElements . ' / ' . $totalElements . ' ' . $elementsName; ?>
        |
        <?php if (isset($order)) { ?>
            <?= \mpf\web\helpers\Form::get()->openForm(['method' => 'get', 'style' => 'display:inline;']); ?>
            <?= \mpf\modules\forum\components\Translator::get()->translate('Order By'); ?>
            <?= \mpf\web\helpers\Form::get()->select('order', \mpf\modules\forum\models\ForumReply::getOrdersForSelect(), $order, ['onchange' => 'this.form.submit();']); ?>
            <?= \mpf\web\helpers\Form::get()->closeForm(); ?>
        <?php } ?>
    </span>
    <?php if ($totalPages > 1) { ?>
        <ul class="forum-page-list-pages">
            <?php if ($currentPage > 1) { ?>
                <li>
                    <?= $this->getPageLink(1, 1); ?>
                </li>
                <li>
                    <?= ($currentPage - 5 > 2) ? \mpf\web\helpers\Html::get()->tag('span', '...', ['class' => 'forum-dots-for-missing-pages']) : ''; ?>
                </li>
                <?php for ($i = 5; $i > 0; $i--) { ?>
                    <li>
                        <?= (($currentPage > $i) && (($currentPage - $i) != 1)) ? $this->getPageLink($currentPage - $i, $currentPage - $i) : ''; ?>
                    </li>
                <?php } ?>
            <?php } ?>
            <li>
                <input type="text" url-template="<?= $this->getPageURL(999999); ?>" name="page"
                       class="forum-page-input" value="<?= $currentPage; ?>"/>
            </li>
            <?php if ($currentPage < $totalPages) { ?>
                <?php for ($i = 1; $i < 6; $i++) { ?>
                    <li>
                        <?= ((($totalElements - $currentPage) >= $i) && (($currentPage + $i) < $totalPages)) ? $this->getPageLink($currentPage + $i, $currentPage + $i) : ''; ?>
                    </li>
                <?php } ?>
                <li>
                    <?= ($currentPage + 5 < $totalPages - 1) ? \mpf\web\helpers\Html::get()->tag('span', '...', ['class' => 'forum-dots-for-missing-pages']) : ''; ?>
                </li>
                <li>
                    <?= $this->getPageLink($totalPages, $totalPages); ?>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>