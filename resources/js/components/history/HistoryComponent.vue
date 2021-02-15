<template>
    <DefaultLayout>
        <template v-slot:title>
            История
        </template>
        <template v-slot:content>
            <div
                class="mb-3"
                v-for="history in historyData"
                @key="history.id"
            >
                <b-message type="is-info">
                    {{ history.user.first_name }} ({{ history.user.role[0].name}}) отредактировал поле.
                    Было: <span class="text-success">{{ JSON.parse(history.before).value }}</span>,
                    Стало: <span class="text-danger">{{ history.after }}</span><br>
                    <small>
                        <b class="text-black-50">{{ createdAt(history.created_at) }}</b>
                    </small>
                </b-message>
            </div>
        </template>
    </DefaultLayout>
</template>

<script>
import DefaultLayout from "../layouts/DefaultLayout";
import historyService from "../../services/history/historyService";
import datetimeMixin from "../../mixins/datetimeMixin";
export default {
    name: "HistoryComponent",
    mixins: [datetimeMixin],
    components: {
        DefaultLayout
    },
    data: () => ({
        historyData: []
    }),
    async mounted() {
         this.historyData = await historyService.getAllHistory();
    }
}
</script>

<style scoped>

</style>
