<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-card
    mc-confirm-button
    mc-status
    mdc-my-festivities
');
?>
<section class="panel--meus-cadastros">
    <h2 class="panel--meus-cadastros__title"><?= $this->text('title', i::__('Meus cadastros')) ?></h2>

    <mc-card v-if="registrations.length > 0">
        <template v-for="registration in registrations" :key="registration.id">
            <?php // O rascunho tem continuar e excluir; o enviado é a linha toda como link. ?>
            <div v-if="registration.status == 0" class="registro-row registro-row--draft">
                <div>
                    <div class="registro-row__title">{{ title(registration) }}</div>
                    <div class="registro-row__meta">{{ meta(registration) }}</div>
                </div>
                <div class="registro-row__actions">
                    <mc-status :status-name="statusName(registration)"></mc-status>
                    <mc-confirm-button @confirm="remove(registration)">
                        <template #button="modal">
                            <button type="button" class="button button--sm button--text button--text-danger" :aria-label="`${text('excluir')}: ${title(registration)}`" @click="modal.open()">{{ text('excluir') }}</button>
                        </template>
                        <template #message>
                            {{ text('confirmar exclusão') }}
                        </template>
                    </mc-confirm-button>
                    <a :href="registration.singleUrl" class="button button--sm button--primary-outline">{{ text('continuar') }}</a>
                </div>
            </div>
            <a v-else class="registro-row" :class="{ 'registro-row--pending': pending(registration) }" :href="registration.singleUrl">
                <div>
                    <div class="registro-row__title">{{ title(registration) }}</div>
                    <div class="registro-row__meta">{{ meta(registration) }}</div>
                </div>
                <mc-status :status-name="statusName(registration)"></mc-status>
            </a>
        </template>
    </mc-card>

    <?php // Os festejos, na mesma lista de "Meus festejos". ?>
    <mdc-my-festivities v-if="festivities.length > 0" :festivities="festivities" :more="moreFestivities"></mdc-my-festivities>

    <p v-if="!loading && !failed && registrations.length === 0 && festivities.length === 0" class="panel--meus-cadastros__empty">
        <?php i::_e('Você ainda não tem cadastros.') ?>
    </p>
</section>
