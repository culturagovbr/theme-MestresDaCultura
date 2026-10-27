<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-icon
');
?>
<section class="home-mestres">
    <h2 class="home-mestres__title"><?= $this->text('title', i::__('Mestras e Mestres em destaque')) ?></h2>

    <div v-if="masters.length" class="mestres-grid">
        <div v-for="master in masters" :key="master.id" class="entity-card">
            <div class="entity-card__header">
                <div class="mc-avatar">
                    <img v-if="master.avatar" :src="master.avatar" alt="">
                    <mc-icon v-else name="agent-1"></mc-icon>
                </div>
                <div>
                    <div class="entity-card__title">{{ master.knownAs || master.name }}</div>
                    <div class="entity-card__subtitle">{{ subtitle(master) }}</div>
                </div>
            </div>
            <div v-if="master.summary" class="entity-card__content-shortDescription">{{ master.summary }}</div>
            <div class="entity-card__footer">
                <a class="button button--secondary-outline button--sm" :href="master.url"><?php i::_e('Ver perfil') ?></a>
            </div>
        </div>
    </div>
    <p v-else class="home-mestres__empty"><?php i::_e('Nenhuma mestra ou mestre cadastrado ainda.') ?></p>
</section>
