<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-card
    mc-status
    mdc-my-festivities
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

    <?php // Os festejos, na mesma lista de "Meus festejos". ?>
    <mdc-my-festivities v-if="festivities.length > 0" :festivities="festivities"></mdc-my-festivities>

    <p v-if="!loading && !failed && registrations.length === 0 && festivities.length === 0" class="panel--meus-cadastros__empty">
        <?php i::_e('Você ainda não tem cadastros.') ?>
    </p>
</section>
