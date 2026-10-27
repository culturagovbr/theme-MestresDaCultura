<?php
/**
 * @var MapasCulturais\App $app
 * @var MapasCulturais\Themes\BaseV2\Theme $this
 */

use MapasCulturais\i;

$this->import('
    mc-header-menu
    mc-header-menu-user
    mc-icon
    mc-messages 
    theme-logo 
');

$redirect_to = $_SERVER['REQUEST_URI'] ?? '/';
$current_controller = $this->controller?->id;
?>
<?php $this->applyTemplateHook('main-header', 'before') ?>
<header class="main-header" id="main-header">
    <?php $this->applyTemplateHook('main-header', 'begin') ?>

    <div class="main-header__content">

        <?php $this->applyTemplateHook('mc-header-menu', 'before') ?>
        <mc-header-menu>

            <!-- Logo -->
            <template #logo>
                <theme-logo href="<?= $app->createUrl('site', 'index') ?>"></theme-logo>
            </template>
            <!-- Menu principal -->
            <template #default>
                <?php $this->applyTemplateHook('mc-header-menu', 'begin') ?>
                
                <?php $this->applyTemplateHook('mc-header-menu-home', 'before') ?>
                <li>
                    <?php $this->applyTemplateHook('mc-header-menu-home', 'begin') ?>
                    <a href="<?= $app->createUrl('site', 'index') ?>" class="mc-header-menu--item<?= $current_controller === 'site' ? ' active' : '' ?>">
                        <span class="icon"> <mc-icon name="home"></mc-icon> </span>
                        <p class="label"> <?php i::_e('Home') ?> </p>
                    </a>
                    <?php $this->applyTemplateHook('mc-header-menu-home', 'end') ?>
                </li>
                <?php $this->applyTemplateHook('mc-header-menu-home', 'after') ?>

                <?php // A busca pública de Mestras e Mestres (plugin MDC, /mestras-e-mestres/). ?>
                <li>
                    <a href="<?= $app->createUrl('mdc', 'masters') ?>" class="mc-header-menu--item<?= $current_controller === 'mdc' && $this->controller?->action === 'masters' ? ' active' : '' ?>">
                        <span class="icon"> <mc-icon name="agent-2"></mc-icon> </span>
                        <p class="label"> <?php i::_e('Mestras e<br>Mestres') ?> </p>
                    </a>
                </li>

                <li>
                    <a href="<?= $app->createUrl('mdc', 'index') ?>" class="mc-header-menu--item">
                        <span class="icon"> <mc-icon name="master"></mc-icon> </span>
                        <p class="label"> <?php i::_e('Cadastro de<br>Mestres e Mestras') ?> </p>
                    </a>
                </li>

                <li>
                    <a href="#" class="mc-header-menu--item">
                        <span class="icon"> <mc-icon name="celebration"></mc-icon> </span>
                        <p class="label"> <?php i::_e('Festejos<br>Populares') ?> </p>
                    </a>
                </li>

                <li>
                    <a href="<?= $app->createUrl('panel', 'index') ?>" class="mc-header-menu--item<?= $current_controller === 'panel' ? ' active' : '' ?>">
                        <span class="icon"> <mc-icon name="dashboard"></mc-icon> </span>
                        <p class="label"> <?php i::_e('Painel de<br>Controle') ?> </p>
                    </a>
                </li>
                
                <?php $this->applyTemplateHook('mc-header-menu', 'end') ?>
            </template>

        </mc-header-menu>
        <?php $this->applyTemplateHook('mc-header-menu', 'after') ?>

        <div class="main-header__buttons">
            <?php $this->applyTemplateHook('mc-header-menu-user', 'before') ?>
            <?php if ($app->user->is('guest')): ?>
                <!-- Botão login -->
                <a href="<?= $app->createUrl('auth') ?>?redirectTo=<?=$redirect_to?>" class="logIn">
                    <?php i::_e('Entrar') ?>
                </a>
            <?php else: ?>
                <!-- Menu do usuário -->
                <mc-header-menu-user></mc-header-menu-user>
            <?php endif; ?>
            <?php $this->applyTemplateHook('mc-header-menu-user', 'after') ?>
        </div>

    </div>

    <?php $this->applyTemplateHook('main-header', 'end') ?>
</header>
<?php $this->applyTemplateHook('main-header', 'after') ?>

<mc-messages></mc-messages>