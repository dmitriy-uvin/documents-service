<template>
    <span></span>
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
    }
}
</script>

<style scoped>

</style>
