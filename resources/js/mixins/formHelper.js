/**
 * Form helper mixin. This mixin is usefull if you have server-side validation, you can
 * use this to manipulate error stacks.
 */

export const formHelper = {
    data() {
        return {
            form : {},
            form_errors: []
        }
    },
    methods : {
        /**
         * Check if given field has error entry in the error stack
         *
         * @param field
         * @param returnMessage
         *
         * @return <mixed>
         */
        hasError(field, returnMessage = true) {
            if (this.form_errors[field]){
                return returnMessage ? this.form_errors[field][0] : true
            } else {
                return returnMessage ? null : false
            }
        },

        /**
         * Reset and re-populates the error stack
         *
         * @param errors <array> - array of key value pair errors
         */
        setErrors(errors) {
            if (typeof errors !== 'object') return

            this.form_errors = []
            this.form_errors = errors
        },

        /**
         * Clear the error stack
         */
        resetErrors() {
            this.form_errors = []
        },

        /**
         * Add new custom error message to the field
         * param field - field name - bases for the call for hasError()
         * message - error message to display
         */
        addError(field, message) {
            this.form_errors[field] = message
        },

        /**
         * Removes errors based on given field name(s)
         * param fields array - the field to be removed
         */
        removeErrors(fields) {
            let errors = []
            for (let x in this.form_errors) {
                if (fields.includes(x)) continue
                errors[x] = this.form_errors[x]
            }
            this.form_errors = errors
        }
    }
}
