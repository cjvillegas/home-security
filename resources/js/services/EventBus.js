import Vue from 'vue'

export default class EventBus {
    constructor(){
        this.vue = new Vue()
    }

    /**
     * Fires and event
     */
    fire(event, data = null) {
        this.vue.$emit(event, data)
    }

    /**
     * Listen to fired event
     */
    listen(event, callback) {
        this.vue.$on(event, callback)
    }

    // Destroy an event or all events
    destroy(event = null, callback = null)
    {
        if (event == null && callback == null) {
            this.vue.$off()
            return
        } else if (event == null) {
            this.vue.$off([event])
            return
        }

        this.vue.$off([event, callback])
    }
}
