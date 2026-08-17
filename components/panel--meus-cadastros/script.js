app.component('panel--meus-cadastros', {
    template: $TEMPLATES['panel--meus-cadastros'],

    setup() {
        // os textos estão localizados no arquivo texts.php deste componente
        const text = Utils.getTexts('panel--meus-cadastros')
        return { text }
    },

    data() {
        return {
            loading: true,
            registrations: [],
        }
    },

    async created() {
        const registrationAPI = new API('registration');

        try {
            this.registrations = await registrationAPI.find({
                '@select': 'id,number,status,singleUrl,opportunity.{name},sentTimestamp,updateTimestamp',
                '@order': 'updateTimestamp DESC',
                '@permissions': 'view',
                'status': 'GTE(0)',
                'user': 'EQ(@me)',
            });
        } finally {
            this.loading = false;
        }
    },

    methods: {
        title(registration) {
            return registration.opportunity?.name || `${this.text('inscrição')} ${registration.number}`;
        },

        statusName(registration) {
            return registration.status == 0 ? this.text('não enviada') : this.text('enviada');
        },

        meta(registration) {
            const enviada = registration.status != 0 && registration.sentTimestamp;
            const data = enviada ? registration.sentTimestamp : registration.updateTimestamp;

            if (!data) {
                return '';
            }

            const rotulo = enviada ? this.text('enviado em') : this.text('última edição em');

            return `${rotulo} ${data.date('2-digit year')}`;
        },
    },
});
