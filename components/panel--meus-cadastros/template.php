<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-card
    mc-status
');
?>
<section class="panel--meus-cadastros">
    <h2 class="panel--meus-cadastros__title"><?= $this->text('title', i::__('Meus cadastros')) ?></h2>

    <mc-card v-if="registrations.length > 0">
        <a v-for="registration in registrations" :key="registration.id" class="registro-row" :class="{ 'registro-row--pending': pending(registration) }" :href="registration.singleUrl">
            <div>
                <div class="registro-row__title">{{ title(registration) }}</div>
                <div class="registro-row__meta">{{ meta(registration) }}</div>
            </div>
            <mc-status :status-name="statusName(registration)"></mc-status>
        </a>
    </mc-card>

    <?php // Os festejos: o rascunho volta ao formulário; o publicado abre a página e também se edita pelo formulário. ?>
    <mc-card v-if="festivities.length > 0">
        <div v-for="festivity in festivities" :key="festivity.id" class="registro-row registro-row--festivity" :class="{ 'registro-row--published': festivity.status > 0 }">
            <div>
                <div class="registro-row__title">{{ festivity.name }}</div>
                <div class="registro-row__meta">{{ festivityMeta(festivity) }}</div>
            </div>
            <div class="registro-row__actions">
                <mc-status :status-name="festivity.status > 0 ? text('publicado') : text('rascunho')"></mc-status>
                <template v-if="festivity.status > 0">
                    <a :href="festivity.singleUrl" class="button button--sm button--text">{{ text('ver') }}</a>
                    <a :href="festivityUrl(festivity)" class="button button--sm button--primary-outline">{{ text('editar') }}</a>
                </template>
                <a v-else :href="festivityUrl(festivity)" class="button button--sm button--primary">{{ text('continuar cadastro') }}</a>
            </div>
        </div>
    </mc-card>

    <p v-if="!loading && registrations.length === 0 && festivities.length === 0" class="panel--meus-cadastros__empty">
        <?php i::_e('Você ainda não tem cadastros.') ?>
    </p>
</section>
