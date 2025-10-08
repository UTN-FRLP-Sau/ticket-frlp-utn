new Vue({
    el: "#formCreateUser",

	data: {
		selectClaustro: "",
        es_estudiante: false,
    },

    methods: {
        esEstudiante: function () {
            if (this.selectClaustro == "Estudiante") {
                this.es_estudiante = true;
            } else {
                this.es_estudiante = false;
            }
		},
    },
});
