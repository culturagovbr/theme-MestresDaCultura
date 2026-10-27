<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mdc-master-card
    mdc-network-map
');
?>
<section class="home-mestres">
    <h2 class="home-mestres__title"><?= $this->text('title', i::__('Mestras e Mestres em destaque')) ?></h2>

    <template v-if="masters.length">
        <div class="mestres-grid">
            <mdc-master-card v-for="master in masters" :key="master.id" :master="master"></mdc-master-card>
        </div>
        <a class="home-mestres__all button button--primary-outline" href="<?= $app->createUrl('mdc', 'masters') ?>"><?php i::_e('Ver todas e todos') ?></a>
    </template>
    <p v-else class="home-mestres__empty"><?php i::_e('Nenhuma mestra ou mestre cadastrado ainda.') ?></p>

    <mdc-network-map></mdc-network-map>
</section>
