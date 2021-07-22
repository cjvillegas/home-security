import axios from 'axios'

let basePath = '/employee'

export default {
    /**
     * API endpoint to retrieve an employee by barcode
     *
     * @param barcode
     *
     * @return Promise
     */
    getEmployeeByBarcode(barcode) {
        return axios.get(`${basePath}/get-employee-by-barcode?barcode=${barcode}`)
    },
}
