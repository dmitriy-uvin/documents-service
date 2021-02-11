<template>
    <DefaultLayout>
        <template v-slot:title>
            Задания
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
    name: "TasksComponent",
    components: {
        DefaultLayout
    }
}
</script>

<style scoped>

</style>
