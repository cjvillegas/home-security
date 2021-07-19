import moment from 'moment'

export default class StringGenericService {
    /**
     * formats the given datetime string | moment date to the specified
     * moment accepted format
     *
     * @param datetime
     * @param baseFormat
     * @param format
     *
     * @return string
     */
    formatDateTime(datetime, baseFormat = 'MM/DD/YYYY HH:mm:ss', format = 'YYYY-MM-DD HH:mm') {
        // sanity check if datetime is present
        if (!datetime) {
            return ''
        }

        // if passed datetime is already an instance of moment
        if (moment.isMoment(datetime)) {
            return datetime.format(format)
        }

        return moment(datetime, baseFormat).format(format)
    }

    /**
     * Get the hour portion of the provided time string format
     *
     * @param time
     *
     * @return string|null
     */
    getHoursFromTimeFormat(time) {
        // sanity check if a valid time is supplied
        if (!time && typeof time !== 'string') {
            return null;
        }

        // split the string and only retrieve the first element
        // which is the hour section of the time string format
        let exploded = time.split(':', 1)

        // returns the first value from the exploded time format, null if not present
        return exploded[0] || null
    }
}
