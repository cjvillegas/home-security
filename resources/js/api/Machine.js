import axios from 'axios'

export default {
     /**
      * Main API endpoint in fetching all Machines
      *
      * @param data
      *
      * @handler \App\Http\Controllers\Admin\MachineController@fetchMachines
      *
      * @return Promise
     */
    fetch(data) {
        let apiUrl = `/admin/machines/machines-list`

        return axios.post(apiUrl, data)
    },

    /**
     * Save Machine API
     *
     * @handler \App\Http\Controllers\Admin\MachineController@store
     *
     * @return Promise
     */
    save(data) {
        let apiUrl = `/admin/machines/store`

        return axios.post(apiUrl, data)
    },

    /**
     * @handler \App\Http\Controllers\Admin\MachineController@allMachine
     *
     * @returns Promise
     */
    allMachines() {
        return axios.get(`/admin/machines/all-machines`)
    }
}
