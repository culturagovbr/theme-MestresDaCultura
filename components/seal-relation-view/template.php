<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-icon
    theme-logo
');
?>
<?php // Certificado do selo do programa (ver MDC\Services\ProgramSeal), no lugar da página genérica do core. ?>
<div :class="classes" class="seal-relation-view mdc-certificate">
    <div class="mdc-certificate__actions">
        <a class="button button--text button--icon" href="javascript:history.back()">
            <mc-icon name="arrow-left"></mc-icon> <?php i::_e('Voltar') ?>
        </a>
        <button type="button" class="button button--primary button--icon" @click="print()">
            <mc-icon name="print"></mc-icon> <?php i::_e('Imprimir ou salvar em PDF') ?>
        </button>
    </div>

    <div id="print">
        <article class="mdc-certificate__sheet">
            <theme-logo></theme-logo>
            <p class="mdc-certificate__eyebrow"><?php i::_e('Certificado') ?></p>
            <h1 class="mdc-certificate__title">{{ seal.name }}</h1>
            <p class="mdc-certificate__text" v-html="certificateText"></p>
            <p class="mdc-certificate__date"><?php i::_e('Emitido em') ?> {{ dateCreated }}</p>
            <p class="mdc-certificate__issuer"><?php i::_e('Ministério da Cultura · Secretaria de Cidadania e da Diversidade Cultural') ?></p>
        </article>
    </div>
</div>
