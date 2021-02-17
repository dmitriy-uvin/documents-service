<template>
    <div class="classification">
        <div v-for="(tasks, taskKey) in details" v-if="Object.keys(details).length">
            {{ taskKey }}
            <div
                class="row mb-3"
                v-for="task in tasks"
            >
                <div class="col-md-6">
                    <img :src="'storage/' + task.document_path" alt="Document Crop image"/>
                </div>
                <div class="col-md-6">
                    <h2 class="subtitle">
                        Document Type: <b>{{ task.document_type }}</b>
                    </h2>
                    <div class="fields-data" v-if="recognizedDataExists(taskKey, task.id)">
                        <div class="row mb-2">
                            <div class="col-md-4 text-left">
                                <b>Field Name</b>
                            </div>
                            <div class="col-md-5 text-right">
                                <b>Value</b>
                            </div>
                            <div class="col-md-3 text-right">
                                <b>Confidence</b>
                            </div>
                        </div>
                        <div
                            class="row mb-2"
                            v-for="(field, field_name) in recognizedData[taskKey][task.id].fields"
                        >
                            <div class="col-md-4 text-left">
                                {{ field_name }}
                            </div>
                            <div class="col-md-5 text-right">
                                {{ field.text }}
                            </div>
                            <div class="col-md-3 text-right">
                                <span
                                    class="confidence-badge"
                                    :class="'confidence-' + getLevelOfConfidence(field.confidence)"
                                >
                                    {{ field.confidence.toFixed(2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-danger"
                         v-if="!Object.keys(recognizableDocTypes).includes(task.document_type)"
                    >
                        Документ не может быть распознан!
                    </div>
                </div>
            </div>
            <div class="buttons d-flex justify-content-center">
                <div class="col-md-4 row">
                    <div
                        :class="recognizedData[taskKey].recognized ? 'col-md-12' : 'col-md-6'"
                        v-if="recognizedData[taskKey].recognized && !recognizedData[taskKey].saved"
                    >
                        <b-button
                            type="is-success"
                            expanded
                            @click="saveIndividual(taskKey)"
                            :loading="recognizedData[taskKey].loading"
                        >
                            Сохранить
                        </b-button>
                    </div>
                    <div
                        :class="!recognizedData[taskKey].recognized ? 'col-md-12' : 'col-md-6'"
                        v-if="!recognizedData[taskKey].recognized"
                    >
                        <b-button
                            type="is-info"
                            expanded
                            @click="onRecognizeByTaskKey(taskKey)"
                            :loading="recognizedData[taskKey].loading"
                        >
                            Распознать
                        </b-button>
                    </div>
                    <div
                        class="col-md-12"
                        v-if="recognizedData[taskKey].saved"
                    >
                        <b-button
                            type="is-primary"
                            expanded
                            @click="goToIndividual(taskKey)"
                        >
                            Перейти
                        </b-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import documentService from "../../services/document/documentService";
import EventBus from "../../events/eventBus";
import documentTypes from "../../constants/documentTypes";
export default {
    name: "ClassificationComponent",
    props: ['details'],
    data: () => ({
        recognizedData: {},
        recognizableDocTypes: documentTypes.recognizable,
        goTo: {

        }
    }),
    watch: {
        details() {
            Object.keys(this.details).map(key => {
                if (!this.recognizedData[key]) {
                    this.recognizedData[key] = {};
                }
                this.details[key].map(task => {
                    this.recognizedData[key][task.id] = {};
                    this.recognizedData[key][task.id].loading = false;
                    this.recognizedData[key][task.id].id = task.id;
                });
                this.recognizedData[key].loading = false;
                this.recognizedData[key].recognized = false;
                this.recognizedData[key].saved = false;
            });
        }
    },
    methods: {
        async onRecognizeByTaskKey(taskKey) {
            try {
                this.changePropertyForTaskKey('loading', taskKey, true);
                const response = await documentService.getRecognizeResponseByTaskKey({
                    task_key: taskKey
                });
                this.changePropertyForTaskKey('loading', taskKey, false);

                const groupedResult = _.groupBy(response, 'task_id');
                const result = {};
                Object.keys(groupedResult).map(taskKey => {
                    if (!result[taskKey]) result[taskKey] = {};
                    Object.keys(groupedResult[taskKey]).map(index => {
                        const task = groupedResult[taskKey][index];
                        if (!result[taskKey][task.id]) result[taskKey][task.id] = {};
                        result[taskKey][task.id] = groupedResult[taskKey][index];
                    });
                });
                this.recognizedData = {
                    ...this.recognizedData,
                    ...result
                };
                this.changePropertyForTaskKey('recognized', taskKey, true);
            } catch (error) {
                this.changePropertyForTaskKey('loading', taskKey, false);
                EventBus.$emit('error', error.message);
            }
        },
        goToIndividual(taskKey) {
            window.location.href = '/individuals/' + this.goTo[taskKey];
        },
        async saveIndividual(taskKey) {
            try {
                const payloadRecognizedData = {};
                Object.keys(this.recognizedData).map(mainKey => {
                    Object.keys(this.recognizedData[mainKey]).map(task => {
                        if (!payloadRecognizedData[mainKey])
                            payloadRecognizedData[mainKey] = {};

                        if (Object.keys(this.recognizedData[mainKey][task]).length) {
                            if (!payloadRecognizedData[mainKey][task])
                                payloadRecognizedData[mainKey][task] = {};

                            payloadRecognizedData[mainKey][task] = {
                                id: this.recognizedData[mainKey][task].id,
                                fields: this.recognizedData[mainKey][task].fields,
                                document_type: this.recognizedData[mainKey][task].doc_type
                            };
                        }
                    });
                });
                this.changePropertyForTaskKey('loading', taskKey, true);
                const response = await documentService.saveIndividual({
                    payloadData: payloadRecognizedData
                });
                this.goTo = {
                    ...this.goTo,
                    [taskKey]: response[taskKey]
                };
                this.changePropertyForTaskKey('loading', taskKey, false);
                this.changePropertyForTaskKey('saved', taskKey, true);
                EventBus.$emit('success', 'Физическое лицо было добавлено!');
            } catch(error) {
                this.changePropertyForTaskKey('loading', taskKey, false);
                EventBus.$emit('error', error.message);
            }
        },
        getLevelOfConfidence(confidence) {
            if (confidence >= 0 && confidence < 0.49) return 'low';
            if (confidence >= 0.49 && confidence < 0.7) return 'middle';
            if (confidence > 0.7) return 'high';
        },
        recognizedDataExists(taskKey, taskId) {
            if (this.recognizedData[taskKey][taskId]) {
                if (this.recognizedData[taskKey][taskId].fields) {
                    return Object.keys(this.recognizedData[taskKey][taskId].fields).length;
                }
            }
            return false;
        },
        changePropertyForTaskKey(property, taskKey, value) {
            this.recognizedData = {
                ...this.recognizedData,
                [taskKey]: {
                    ...this.recognizedData[taskKey],
                    [property]: value
                }
            };
        }
    }
}
</script>

<style scoped>
</style>
