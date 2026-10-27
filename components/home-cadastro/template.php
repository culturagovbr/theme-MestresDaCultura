<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

$this->import('mc-icon');
?>
<aside class="home-cadastro">
    <div class="section-card">
        <div class="section-card__title">
            <mc-icon name="master"></mc-icon>
            <?= $this->text('title-mestre', i::__('Sou mestre ou mestra')) ?>
        </div>
        <p class="section-card__subtitle"><?= $this->text('description-mestre', i::__('Cadastre-se na Rede Nacional e gere seu portfólio automaticamente.')) ?></p>
        <a class="button button--secondary button--large" href="<?= $app->createUrl('mdc', 'index') ?>"><?php i::_e('Fazer meu cadastro') ?></a>
    </div>

    <div class="section-card">
        <div class="section-card__title">
            <mc-icon name="celebration"></mc-icon>
            <?= $this->text('title-festejo', i::__('Cadastrar Festejo Popular')) ?>
        </div>
        <p class="section-card__subtitle"><?= $this->text('description-festejo', i::__('Qualquer pessoa ou organização pode cadastrar um festejo popular.')) ?></p>
        <a class="button button--primary-outline button--large" href="<?= $app->createUrl('mdc', 'festivity') ?>"><?php i::_e('Cadastrar festejo') ?></a>
    </div>
</aside>
