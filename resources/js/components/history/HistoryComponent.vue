<template>
    <DefaultLayout>
        <template v-slot:title>
            История
        </template>
        <template v-slot:content>
            <b-message type="is-warning">В разработке!
            </b-message>
        </template>
    </DefaultLayout>
</template>

<script>
import DefaultLayout from "../layouts/DefaultLayout";
export default {
    name: "HistoryComponent",
    components: {
        DefaultLayout
    }
}
</script>

<style scoped>

</style>
