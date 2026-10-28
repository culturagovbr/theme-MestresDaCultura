<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

// Os números vêm do plugin MDC: mestres e unidades da federação (MasterShowcase) e festejos publicados (FestivitySeal).
?>
<section class="tema-banner">
    <h1><?= $this->text('title', i::__('Rede Nacional de Mestras e Mestres das Culturas Tradicionais e Populares')) ?></h1>
    <p><?= $this->text('description', i::__('Cadastre-se, dê visibilidade ao seu trabalho e fortaleça os vínculos comunitários das culturas tradicionais e populares do Brasil.')) ?></p>

    <div class="badges">
        <div class="badge">
            <strong>{{ counts.masters }}</strong>
            <span><?php i::_e('Mestras e Mestres cadastrados') ?></span>
        </div>
        <div class="badge">
            <strong>{{ counts.festivities }}</strong>
            <span><?php i::_e('Festejos Populares cadastrados') ?></span>
        </div>
        <div class="badge">
            <strong>{{ counts.states }}</strong>
            <span><?php i::_e('Unidades da Federação') ?></span>
        </div>
    </div>
</section>
