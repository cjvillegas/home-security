import axios from 'axios'

export default {
    /**
     * Retrieve scanners with same blindid
     *
     * @param toSearch
     * @param field
     *
     * @return Promise
     */
    getScannersByField(field, toSearch) {
        return axios.get(`/admin/scanners/get-scanners-by-field?field=${field}&toSearch=${toSearch}`)
    },

    /**
     * API endpoint that searches and scanners based on the provided field
     * the field parameter is a DB column and the search string is the
     * search query
     *
     * @return Promise
     */
    searchScannerByField(field, searchString) {
        return axios.get(`/admin/scanners/search-scanners-by-field?field=${field}&searchString=${searchString}`)
    },
}
