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
<panel--meus-cadastros></panel--meus-cadastros>
