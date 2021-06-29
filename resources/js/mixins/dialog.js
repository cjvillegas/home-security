export const dialog = {
    props: {
        visible: {
            type: Boolean,
            required: true,
            default: false
        }
    },
    data() {
        return {
            showDialog: false,
        }
    },
    watch: {
        visible(){
            this.showDialog = this.visible
        }
    },
    methods: {
        closeModal() {
            this.showDialog = false
            setTimeout(() => {
                this.$emit('close')
            }, 100)
        },
    },
    mounted() {
        this.showDialog = this.visible;
    }
}
