import axios from 'axios'

export default {
    /**
     * API endpoint in fetching list of process sequence link list
     * This route is under the API collection
     *
     * @param processSequenceId
     *
     * @return Promise
     */
    getProcessSequenceSteps(processSequenceId) {
        return axios.get(`/admin/process-sequence/${processSequenceId}/steps`)
    },

    /**
     * API endpoint in storing new process sequence link.
     * This route is under the API collection
     *
     * @param processSequenceId
     * @param postData
     *
     * @return Promise
     */
    store(processSequenceId, postData) {
        return axios.post(`/admin/process-sequence/${processSequenceId}/add-new-step`, postData)
    },

    /**
     * API endpoint for deleting a process sequence link.
     * This route is under the API collection
     *
     * @param processSequenceId
     * @param stepId
     *
     * @return Promise
     */
    delete(processSequenceId, stepId) {
        return axios.delete(`/admin/process-sequence/${processSequenceId}/delete-step/${stepId}`)
    },

    /**
     * API endpoint for modifying the sequence order
     *
     * @param processSequenceId
     * @param currentStepId
     * @param direction
     *
     * @return Promise
     */
    moveStepOrder(processSequenceId, currentStepId, direction) {
        return axios.put(`/admin/process-sequence/${processSequenceId}/move-step-order/${currentStepId}?direction=${direction}`)
    }
}
