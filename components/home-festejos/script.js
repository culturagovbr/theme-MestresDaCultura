app.component('home-festejos', {
    template: $TEMPLATES['home-festejos'],

    data() {
        // Os festejos em destaque que o plugin MDC monta (FestivityShowcase): os publicados mais recentes primeiro.
        return {
            festivities: $MAPAS.config.mdcShowcase?.festivities ?? [],
        };
    },
});
