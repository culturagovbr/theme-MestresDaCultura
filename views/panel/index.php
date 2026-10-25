<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

$this->import('
    panel--meus-cadastros
');

$this->breadcrumb = [
    ['label' => i::__('Início'), 'url' => $app->createUrl('site', 'index')],
    ['label' => i::__('Painel de Controle')],
];
?>
<?php // O plugin MDC põe aqui os blocos do papel (moderação, gestão); "Meus cadastros" segue para todos. ?>
<?php $this->applyTemplateHook('mdc-role-blocks', 'before') ?>
<panel--meus-cadastros></panel--meus-cadastros>
