app.component('home-banner', {
    template: $TEMPLATES['home-banner'],

    data() {
        return {
            counts: $MAPAS.config.mdcShowcase?.counts ?? { masters: 0, states: 0 },
        };
    },
});
