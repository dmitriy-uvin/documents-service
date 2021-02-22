<template>
    <div class="history">
        <div
            v-if="historyData.length"
            class="mb-3"
            v-for="history in historyData"
            @key="history.id"
        >
            <b-message :type="history.type.includes('delete') ? 'is-warning' : 'is-info'">
                <span class="author-name">
                    {{ history.author.first_name + ' ' + history.author.second_name + ' ' + history.author.patronymic }}
                </span>
                <span class="author-role">
                    ({{ history.author.role[0].name}})
                </span>
                <span v-if="history.type === 'field'">
                    отредактировал поле - <b>{{ getFieldNameByKey(history.field.type) }}</b>,
                    Документ <b>#{{ history.field.document_id }}</b>
                    До: <span class="text-success">{{ JSON.parse(history.before).value }}</span>,
                    После: <span class="text-danger">{{ JSON.parse(history.after).value }}</span>
                </span>
                <span v-if="history.type === 'document_add'">
                    добавил документ <b>#{{ history.document_id }}</b>
                </span>
                <span v-if="history.type === 'document_update'">
                    обновил документ <b>#{{ history.document_id }}</b>
                </span>
                <span v-if="history.type === 'document_delete'">
                    удалил документ <b>#{{ history.document_id }}</b>
                </span>
                <br>
                <small>
                    <b class="text-black-50">{{ createdAt(history.created_at) }}</b>
                </small>
            </b-message>
        </div>
        <div v-else>
            <b-message type="is-warning">История пуста!</b-message>
        </div>
    </div>

</template>

<script>
import datetimeMixin from "../../mixins/datetimeMixin";
export default {
    name: "History",
    mixins: [datetimeMixin],
    props: ['historyData']
}
</script>

<style scoped>

</style>
