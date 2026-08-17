<?php
/**
 * @var MapasCulturais\App $app
 * @var MestresDaCultura\Theme $this
 */

use MapasCulturais\i;

// Sem origem enquanto o cadastro de mestres não existe.
$mestres = [];
?>
<section class="home-mestres">
    <h2 class="home-mestres__title"><?= $this->text('title', i::__('Mestras e Mestres em destaque')) ?></h2>

    <?php if ($mestres): ?>
        <div class="mestres-grid">
            <?php foreach ($mestres as $mestre): ?>
                <div class="entity-card">
                    <div class="entity-card__header">
                        <div class="mc-avatar"> <mc-icon name="agent-1"></mc-icon> </div>
                        <div>
                            <div class="entity-card__title"><?= $mestre['nome'] ?></div>
                            <div class="entity-card__subtitle"><?= $mestre['atuacao'] ?></div>
                        </div>
                    </div>
                    <div class="entity-card__content-shortDescription"><?= $mestre['resumo'] ?></div>
                    <div class="entity-card__footer">
                        <a class="button button--secondary-outline button--sm" href="<?= $mestre['url'] ?>"><?php i::_e('Ver perfil') ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p class="home-mestres__empty"><?php i::_e('Nenhuma mestra ou mestre cadastrado ainda.') ?></p>
    <?php endif; ?>
</section>
