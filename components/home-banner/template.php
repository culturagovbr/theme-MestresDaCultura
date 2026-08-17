<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

// Zerados até existir o que contar: mestres e festejos ainda não são entidades da plataforma.
$total_mestres = 0;
$total_festejos = 0;
$total_ufs = 0;
?>
<section class="tema-banner">
    <h1><?= $this->text('title', i::__('Rede Nacional de Mestras e Mestres das Culturas Tradicionais e Populares')) ?></h1>
    <p><?= $this->text('description', i::__('Cadastre-se, dê visibilidade ao seu trabalho e fortaleça os vínculos comunitários das culturas tradicionais e populares do Brasil.')) ?></p>

    <div class="badges">
        <div class="badge">
            <strong><?= $total_mestres ?></strong>
            <span><?php i::_e('Mestras e Mestres cadastrados') ?></span>
        </div>
        <div class="badge">
            <strong><?= $total_festejos ?></strong>
            <span><?php i::_e('Festejos Populares cadastrados') ?></span>
        </div>
        <div class="badge">
            <strong><?= $total_ufs ?></strong>
            <span><?php i::_e('Unidades da Federação') ?></span>
        </div>
    </div>
</section>
