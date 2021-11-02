import axios from 'axios'

const BASE_URL = '/admin/in-house/stock-levels'

export default {
    /**
     * Get out of stock items total count
     *
     * @handler Admin\InHouse\StockLevelController@outOfStockTotalCount
     *
     * @return Promise
     */
    fetchOutOfStockItemsTotalCount() {
        return axios.get(`${BASE_URL}/out-of-stock/total-count`)
    },

}
