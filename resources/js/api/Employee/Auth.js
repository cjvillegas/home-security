import axios from 'axios'

let basePath = `/employee`

export default {

    login(postData) {
        return axios.post(`${basePath}/login`, postData)
    }
}
