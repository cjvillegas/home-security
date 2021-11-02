import Vue from 'vue'
import StringGenericService from '../services/StringGenericService'

let stringService = new StringGenericService()

Vue.filter('fixDateTimeByFormat', (date, format = 'MMM DD, YYYY') => {
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

/**
 * Properly format date to a human readable format
 *
 * @param date
 * @param format
 *
 * @return string
 */
Vue.filter('fixDateByFormat', (date, format = 'MMM DD, YYYY') => {
    if (date) {
        if (window.timezone) {
            if (window.timezone === 'Asia/Manila') {
                return moment.utc(date).tz(window.timezone).format(format) + ' MNL'
            }

            return moment.utc(date).tz(window.timezone).format(format)
        }  else {
            return moment.utc(date).local().format(format)
        }
    } else {
        return '--:--'
    }
})

/**
 * Format snake cased word to a proper english word format.
 * ex. this_is_test => This Is Test
 *
 * @param string
 *
 * @return string
 */
Vue.filter('cleanUpSnakeCaseWord', (string) => {
    if (string) {
        return stringService.ucwords(string.replace(/_/g, ' '))
    }

    return ''
})

/**
 * Get the file name from the passed path.
 *
 * @param path
 *
 * @return string
 */
Vue.filter('getFileNameFromPath', (path) => {
    if (path) {
        let explode = path.split('/')

        return explode[explode.length - 1] || ''
    }

    return ''
})

/**
 * Format a string to capitalized all first letters of every word.
 */
Vue.filter('ucWords', (string) => {
    if (string) {
        return stringService.ucwords(string)
    }

    return ''
})

Vue.filter('valueForEmptyText', text => {
    if (!text) {
        return '--:--'
    }

    return text
})
