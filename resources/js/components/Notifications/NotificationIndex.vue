<template>
    <router-view :page-data="pageData"></router-view>
</template>

<script>
    export default {
        name: "NotificationIndex",
        props: {
            pageData: {
                required: true,
                type: Object
            }
        }
    }
</script>
