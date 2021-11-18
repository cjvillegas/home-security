<template>
    <div class="mt-5"
        v-loading="loading">
        <span class="d-flex justify-content-center text-sucess">
            <i class="fa fa-check-circle-o fa-5x" aria-hidden="true"></i>
        </span>
        <span class="d-flex justify-content-center">
            <h1> Thank you, {{ $root.user.name }} </h1>
        </span>
        <span class="d-flex justify-content-center">
            <h2>Your QC Remake Validation Report number is: {{ orderRemakeResponse.report_no }}</h2>
        </span>
        <span class="d-flex justify-content-center mt-5">
            <h3>Your Blind(s) {{ selectedBlinds }}</h3>
        </span>
        <span class="d-flex justify-content-center">
            <h3> Have been verified and ready to be shipped to the customer </h3>
        </span>

        <span class="d-flex justify-content-center mt-5">
            <el-button
                size="medium"
                type="primary"
                @click="printBarcode">
                Print Barcode
                <i class="el-icon-printer el-icon-right">
                </i>
            </el-button>
        </span>
    </div>
</template>


<script>
import { mapGetters } from 'vuex'
    export default {
        name: "RemakeSucess",
        data(){
            return {

            }
        },
        computed: {
            ...mapGetters('remakechecker', ['loading', 'orderRemakeResponse',  'selectedBlindId']),

            selectedBlinds() {
                let formattedArray = []
                this.selectedBlindId.forEach(blind => {
                    formattedArray.push(blind.serial_id)
                })
                return formattedArray.join(", ")
            }
        },
        methods: {
            printBarcode() {
                let baseUrl = window.location.origin

                let content = "<html><head>"
                content += `<link href="${baseUrl}/css/print.css" rel="stylesheet" />`
                content += `<style media="print">
                    @page
                    {
                        margin: 0mm;  /* this affects the margin in the printer settings */
                        size: 89mm 28mm;
                    }
                </style>`


                content += "<body class='text-center'>"
                this.orderRemakeResponse.validated_blinds.forEach(blind => {
                    content += `<div class="text-uppercase f-size-14">QC Verified</div>`
                    content += `<div> ${this.orderRemakeResponse.report_no} - ${blind.barcode} </div>`
                    content += `<svg id="barcode${blind.id}"></svg>`
                })
                content += "</body></head></html>"

                let script = document.createElement("script")
                script.type = "text/javascript"
                script.src = `${baseUrl}/js/jsbarcode.code128.min.js`

                let anotherScript = []
                this.orderRemakeResponse.validated_blinds.forEach(blind => {
                    anotherScript["id" + blind.id] = document.createElement("script")
                    anotherScript["id" + blind.id].text += `setTimeout(_ => {
                        JsBarcode("#barcode${blind.id}", "${blind.barcode}", {
                            height: 35,
                            fontSize: 14
                        })
                    }, 200)`
                })
                let win = window.open("")
                win.document.write(content)
                win.document.body.appendChild(script)
                this.orderRemakeResponse.validated_blinds.forEach(blind => {
                    win.document.body.appendChild(anotherScript["id" + blind.id])
                })
                win.document.close()
                setTimeout(_ => {
                    win.print()
                }, 300)
            }
        }
    }
</script>

<style>

</style>
