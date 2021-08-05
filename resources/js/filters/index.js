import Vue from 'vue'
import StringGenericService from '../services/StringGenericService'

let stringService = new StringGenericService()

Vue.filter('fixDateByFormat', (date, format = 'MMM DD, YYYY') => {
    if (date) {
        if (window.timezone) {
            if (window.timezone === 'Asia/Manila') {
                return moment.utc(date).tz(window.timezone).format(format) + ' MNL'
            }

            return moment.utc(date).tz(window.timezone).format(format)
        } else {
            return moment.utc(date).local().format(format)
        }
    } else {
        return '--:--'
    }
})

Vue.filter('ucWords', (string) => {
    if (string) {
        return stringService.ucwords(string)
    }

    return ''
})
