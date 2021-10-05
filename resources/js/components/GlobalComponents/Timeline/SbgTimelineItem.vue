<template>
    <li class="timeline-item">
        <div class="timeline-badge primary">
            <i class="el-icon-picture"></i>
        </div>
        <div class="circle-label">{{ data.name }}</div>
        <template v-if="data.data && !!data.data.length">
            <div
                v-for="item in data.data"
                class="timeline-panel">
                <div><b>Employee:</b> {{ item.employee | ucWords }}</div>
                <div><b>Clock No.:</b> {{ item.clock_num }}</div>
                <div><b>First Scan:</b> {{ item.first_scan | fixDateByFormat('MMM DD, YYYY HH:mm') }}</div>
                <div><b>Last Scan:</b> {{ item.last_scan | fixDateByFormat('MMM DD, YYYY HH:mm') }}</div>
                <div><b>Clock In:</b> {{ item.clock_in | fixDateByFormat('MMM DD, YYYY HH:mm') }}</div>
                <div><b>Clock Out:</b> {{ item.clock_out | fixDateByFormat('MMM DD, YYYY HH:mm') }}</div>
            </div>
        </template>

        <template
            v-else>
            <div class="timeline-panel">
                <el-empty
                    :image-size="50"
                    class="p-0">
                </el-empty>
            </div>
        </template>
    </li>
</template>

<script>
    export default {
        name: "SbgTimelineItem",

        props: {
            data: {
                type: Object,
                required: true
            }
        },

        data() {
            return {

            }
        }
    }
</script>

<style>
    .circle-label {
        position: absolute;
        font-size: 16px;
        top: -25px;
        color: black;
        font-weight: bolder;
        left: 10px;
    }
</style>
