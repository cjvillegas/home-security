export default {
    data() {
        return {
            functionName: null
        }
    },
    created() {
        this.filters = {...this.filters, ...{
                total: 0,
                size: 25,
                page: 1
            }}
    },
    methods: {
        handleSize(size) {
            this.filters.size = size
            this.filters.page = 1

            this.callFetchFunction()
        },
        handlePage(page) {
            this.filters.page = page

            this.callFetchFunction()
        },
        callFetchFunction() {
            if (this.functionName) {
                setTimeout(_ => {
                    this[this.functionName]()
                }, 100)
            }
        }
    }
}
