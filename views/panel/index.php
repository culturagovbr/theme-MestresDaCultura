<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-tab
    mc-tabs
    panel--meus-cadastros
');

$this->breadcrumb = [
    ['label' => i::__('Início'), 'url' => $app->createUrl('site', 'index')],
    ['label' => i::__('Painel de Controle')],
];
?>
<?php // Abas como no painel do core: a de notificações é a do módulo Notifications, pelo gancho `tabs:end`. ?>
<?php $this->applyTemplateHook('tabs', 'before') ?>
<mc-tabs class="panel-home__tabs">
    <?php $this->applyTemplateHook('tabs', 'begin') ?>
    <mc-tab label="<?php i::esc_attr_e('Principal') ?>" slug="main">
        <?php // O plugin MDC põe aqui os blocos do papel (moderação, gestão); "Meus cadastros" segue para todos. ?>
        <?php $this->applyTemplateHook('mdc-role-blocks', 'before') ?>
        <panel--meus-cadastros></panel--meus-cadastros>
    </mc-tab>
    <?php $this->applyTemplateHook('tabs', 'end') ?>
</mc-tabs>
<?php $this->applyTemplateHook('tabs', 'after') ?>
