app.component('home-mestres', {
    template: $TEMPLATES['home-mestres'],

    data() {
        // A vitrine que o plugin MDC monta (MasterShowcase): os aprovados com o selo, os mais recentes primeiro.
        return {
            masters: $MAPAS.config.mdcShowcase?.highlights ?? [],
        };
    },
});
