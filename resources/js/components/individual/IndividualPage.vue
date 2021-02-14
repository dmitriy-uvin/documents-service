<template>
    <DefaultLayout>
        <template v-slot:title>
            {{ getFullName(individual) }}
        </template>
        <template v-slot:content>
            <div class="documents">
                <div
                    class="document row mb-4"
                    v-for="document in individual.documents"
                    @key="document.id"
                >
                    <div class="col-md-6">
                        <img :src="'/storage/' + document.document_image[0].path" />
                    </div>
                    <div class="col-md-6">
                        <h2 class="subtitle">
                            <b>{{ getDocumentNameByKey(document.type) }}</b>
                        </h2>
                        <div class="row mb-2">
                            <div class="col-md-4 text-left">
                                <b>Название поля</b>
                            </div>
                            <div class="col-md-5 text-right">
                                <b>Значение</b>
                            </div>
                            <div class="col-md-3 text-right">
                                <b>Уверенность</b>
                            </div>
                        </div>
                        <div
                            class="row mb-2"
                            v-for="field in document.fields">
                            <div class="col-md-4 text-left">
                                {{ getFieldNameByKey(field.type) }}
                            </div>
                            <div class="col-md-5 text-right">
                                {{ field.value }}
                            </div>
                            <div class="col-md-3 text-right">
                                <span
                                    class="confidence-badge"
                                    :class="'confidence-' + getLevelOfConfidence(field.confidence)"
                                >
                                    {{ field.confidence }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </DefaultLayout>
</template>

<script>
import DefaultLayout from "../layouts/DefaultLayout";
import individualService from "../../services/individual/individualService";
import individualsMixin from "../../mixins/individualsMixin";
export default {
    name: "IndividualPage",
    components: {
        DefaultLayout
    },
    mixins: [individualsMixin],
    props: ['id'],
    data: () => ({
       individual: []
    }),
    async mounted() {
        try {
            this.individual = await individualService.getIndividualUserById(this.id);
        } catch (error) {
            console.log(error);
        }
    },
    methods: {
        getLevelOfConfidence(confidence) {
            if (confidence >= 0 && confidence < 0.49) return 'low';
            if (confidence >= 0.49 && confidence < 0.7) return 'middle';
            if (confidence > 0.7) return 'high';
        },
    }
}
</script>

<style scoped>

</style>
