<template>
    <div class="classification">
        <div v-for="(tasks, taskKey) in details" v-if="Object.keys(details).length">
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
                            v-for="(field, field_name) in recognizedData[taskKey][task.id].items[0].fields">
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
                                    {{ field.confidence }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <b-button type="is-info"
                              @click="onRecognize(task.id, taskKey)"
                              :loading="recognizedData[taskKey][task.id].loading"
                              v-else
                    >
                        Распознать
                    </b-button>
                </div>
            </div>
            <b-button
                type="is-success"
                @click="saveIndividual(taskKey)"
                :loading="recognizedData[taskKey].loading"
            >
                Сохранить
            </b-button>
        </div>
    </div>
</template>

<script>
import documentService from "../../services/document/documentService";
import EventBus from "../../events/eventBus";
export default {
    name: "ClassificationComponent",
    props: ['details'],
    data: () => ({
        recognizedData: {}
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
            });

            console.log(this.details);
            console.log(this.recognizedData);
        }
    },
    methods: {
        async saveIndividual(taskKey) {
            try {
                const payloadRecognizedData = {};
                // Object.keys(this.recognizedData[taskKey]).map(key => {
                //     if (this.recognizedData[key].items) {
                //         if (payloadRecognizedData[taskKey]) {
                //             payloadRecognizedData[taskKey].push({
                //                 document_type: this.recognizedData[key].items[0].doc_type,
                //                 fields: this.recognizedData[key].items[0].fields,
                //                 id: key
                //             })
                //         } else {
                //             payloadRecognizedData[taskKey] = {
                //                 document_type: this.recognizedData[key].items[0].doc_type,
                //                 fields: this.recognizedData[key].items[0].fields,
                //                 id: key
                //             }
                //         }
                //
                //     }
                // });
                Object.keys(this.recognizedData).map(mainKey => {
                    Object.keys(this.recognizedData[mainKey]).map(task => {
                        if (!payloadRecognizedData[mainKey]) {
                            payloadRecognizedData[mainKey] = {};
                        }
                        if (this.recognizedData[mainKey][task].items) {
                            if (this.recognizedData[mainKey][task].items.length) {
                                if (!payloadRecognizedData[mainKey][task]) {
                                    payloadRecognizedData[mainKey][task] = {};
                                }
                                payloadRecognizedData[mainKey][task] = {
                                    id: this.recognizedData[mainKey][task].id,
                                    fields: this.recognizedData[mainKey][task].items[0].fields,
                                    document_type: this.recognizedData[mainKey][task].items[0].doc_type
                                }
                            }
                        }
                    });
                });
                await documentService.saveIndividual({
                    payloadData: payloadRecognizedData
                });
            } catch(error) {
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
                if (this.recognizedData[taskKey][taskId].items) {
                    return this.recognizedData[taskKey][taskId].items.length;
                }
            }

            return false;
        },
        changeRecognizeLoadingState(taskKey, taskId, value) {
            this.recognizedData = {
                ...this.recognizedData,
                [taskKey]: {
                    ...this.recognizedData[taskKey],
                    [taskId]: {
                        ...this.recognizedData[taskKey][taskId],
                        loading: value
                    }
                }
            };
        },
        async onRecognize(taskId, taskKey) {
            try {
                this.changeRecognizeLoadingState(taskKey, taskId, true);
                const response = await documentService.recognizeTask(taskId);
                const result = {
                    ...this.recognizedData,
                    [taskKey]: {
                        ...this.recognizedData[taskKey],
                        [taskId]: {
                            ...this.recognizedData[taskKey][taskId],
                            ...response
                        }
                    }
                }
                this.recognizedData = result;
                console.log('recognize');
                console.log(this.recognizedData);
            } catch (error) {
                EventBus.$emit('error', error.message);
            }
        }
    }
}
</script>

<style scoped lang="scss">
.confidence-badge {
    padding: 4px 9px;
    border-radius: 18px;
    color: #fff;
}
.confidence {
    &-low {
        background: gray;
    }
    &-middle {
        background: #02c5e0;
    }
    &-high {
        background: #2ac178;
    }
}
</style>
