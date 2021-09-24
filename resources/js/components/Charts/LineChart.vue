<script>
    import { Line, mixins } from 'vue-chartjs'
    const { reactiveProp } = mixins

    export default {
        extends: Line,
        name: "LineChart",
        mixins: [reactiveProp],
        props: {
            chartData: {
                required: true,
                type: Object
            },
            options: {
                type: Object,
                default: {}
            }
        },
        mounted() {
            this.renderChart(this.chartData, this.options)
        }
    }
</script>
