export default {
    data() {
        return {
            savingFilters: false,
            filters: {
                id: null,
                name: null,
                type: null,
                filters: {}
            }
        }
    },

    methods: {
        createFilter() {
            this.savingFilters = true

            this.$API.Filters.store(this.filters)
                .then(res => {
                    this.filters = res.data
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.savingFilters = false
                })
        },

        updateFilter() {
            this.savingFilters = true

            this.$API.Filters.update(this.filters, this.filters.id)
                .then(res => {
                    this.filters = res.data
                })
                .catch(err => {
                    console.log(err)
                })
                .finally(_ => {
                    this.savingFilters = false
                })
        },

        getFilterByType(type) {
            return this.$API.Filters.showByType(type)
                .then(res => {
                    if (res.data && res.data.id) {
                        this.filters = res.data
                    } else {
                        // make sure this is implemented in the component using this mixing and this method
                        this.initializeFilter()
                    }
                })
                .catch(err => {
                    console.log(err)
                })
        },
    }
}
