<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

$this->import('mc-icon');

$termos = $app->config['module.LGPD'] ?? [];
$brand = $app->view->asset('img/logo-footer.svg', false);
?>
<?php $this->applyTemplateHook("main-footer", "before")?>
<div v-if="globalState.visibleFooter" class="main-footer">
    <?php $this->applyTemplateHook("main-footer", "begin")?>
    <div class="main-footer__top">
        <a class="main-footer__brand" href="<?= $app->createUrl('site', 'index') ?>">
            <img src="<?= $brand ?>" alt="">
            <span class="main-footer__brand-text">
                <span><?= htmlspecialchars($app->config['logo.title']) ?></span>
                <strong><?= nl2br(htmlspecialchars($app->config['logo.subtitle']), false) ?></strong>
            </span>
        </a>

        <?php $this->applyTemplateHook("main-footer-links", "before")?>
        <ul class="main-footer__links">
            <?php $this->applyTemplateHook("main-footer-links", "begin")?>
            <?php foreach ($termos as $slug => $termo): ?>
                <li><a href="<?= $app->createUrl('lgpd', 'view', [$slug]) ?>"><?= $termo['title'] ?></a></li>
            <?php endforeach; ?>
            <li><a href="<?= $app->createUrl('faq') ?>"><?php i::_e('Dúvidas Frequentes') ?></a></li>
            <?php $this->applyTemplateHook("main-footer-links", "end")?>
        </ul>
        <?php $this->applyTemplateHook("main-footer-links", "after")?>

        <div class="main-footer__institution">
            <?= $this->text('institution', i::__('Ministério da Cultura · Secretaria de Cidadania e da Diversidade Cultural')) ?>
        </div>
    </div>

    <?php $this->applyTemplateHook("main-footer-reg", "before")?>
    <div class="main-footer__reg">
        <?php $this->applyTemplateHook("main-footer-reg", "begin")?>
        <div class="main-footer__reg-content">
            <?php $this->part('main-footer/developed-by') ?>

            <a class="link" href="https://github.com/culturagovbr/mapadacultura">
                <?php i::_e("Conheça o repositório") ?>
                <mc-icon name="github"></mc-icon>
            </a>
        </div>
        <?php $this->applyTemplateHook("main-footer-reg", "end")?>
    </div>
    <?php $this->applyTemplateHook("main-footer-reg", "after")?>
    <?php $this->applyTemplateHook("main-footer", "end")?>
</div>
<?php $this->applyTemplateHook("main-footer", "after")?>
