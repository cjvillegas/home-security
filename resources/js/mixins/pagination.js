export default {
    data() {
        return {
            functionName: null,
            pagination: {
                total: 0,
                size: 25,
                page: 1
            }
        }
    },
    methods: {
        handleSize(size) {
            this.pagination.size = size
            this.pagination.page = 1

            this.callFetchFunction()
        },
        handlePage(page) {
            this.pagination.page = page

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
