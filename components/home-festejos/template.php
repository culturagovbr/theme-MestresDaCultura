<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mdc-festivity-card
');
?>
<section class="home-festejos">
    <h2 class="home-festejos__title"><?= $this->text('title', i::__('Festejos Populares em destaque')) ?></h2>

    <template v-if="festivities.length">
        <div class="home-festejos__grid">
            <mdc-festivity-card v-for="festivity in festivities" :key="festivity.id" :festivity="festivity"></mdc-festivity-card>
        </div>
        <a class="home-festejos__all button button--primary-outline" href="<?= $app->createUrl('mdc', 'festivities') ?>"><?php i::_e('Ver todos os festejos') ?></a>
    </template>
    <p v-else class="home-festejos__empty"><?php i::_e('Nenhum festejo publicado ainda.') ?></p>
</section>
