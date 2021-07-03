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
}
