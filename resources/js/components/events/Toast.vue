<template>
</template>

<script>
import EventBus from "../../events/eventBus";
import eventsList from "../../events/eventsList";
export default {
    name: "Toast",
    created() {
        EventBus.$on('manager-added', function () {
            this.$buefy.toast.open(eventsList['manager-added'])
        });
        EventBus.$on('user-deleted', function () {
            this.$buefy.toast.open(eventsList['user-deleted'])
        });
        EventBus.$on('error', function (message) {
            this.$buefy.toast.open(eventsList.error(message));
        });
    }
}
</script>

<style scoped>

</style>
