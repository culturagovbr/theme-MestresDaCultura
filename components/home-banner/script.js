app.component('home-banner', {
    template: $TEMPLATES['home-banner'],

    data() {
        return {
            counts: { masters: 0, states: 0, festivities: 0, ...$MAPAS.config.mdcShowcase?.counts },
        };
    },
});
