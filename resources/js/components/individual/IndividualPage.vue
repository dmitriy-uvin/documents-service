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
                                    {{ field.confidence.toFixed(2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <div v-if="!receivedDetails">
                <div class="">
                    <label class="dropzone"
                           :class="{ 'dropzone-active' : dropzoneActive }"
                           for="file-uploader"
                           @drop.prevent="onDrop"
                           @dragover.prevent="onDropzoneHover"
                           @dragleave.prevent="onDropzoneLeave"
                    >
                    <span class="dropzone-title">
                        Выберите файлы для распознавания
                    </span>
                        <span class="dropzone-subtitle">или просто перетащите их сюда</span>
                    </label>
                    <div class="text-center user-select-none">
                        Форматы JPEG, PNG, BMP, TIFF, GIF, PDF, DJVU — весом&nbsp;до&nbsp;10&nbsp;МБ.<br>
                        Поддерживаются многостраничные файлы и&nbsp;распознавание&nbsp;нескольких&nbsp;документов в одном файле.
                    </div>
                    <input
                        id="file-uploader"
                        :accept="availableTypes"
                        multiple=""
                        type="file"
                        autocomplete="off"
                        tabindex="-1"
                        style="display: none;"
                        @change="onChange"
                    />
                </div>
                <div
                    class="files-previews d-flex justify-content-center mb-3"
                    v-if="filesPreviews.length"
                >
                    <div class="file-preview mr-2" v-for="filePreview in filesPreviews">
                        {{ filePreview }}
                    </div>
                </div>
                <div class="buttons d-flex justify-content-center" v-if="documentFiles.length">
                    <div class="mt-3 row col-md-6">
                        <div class="col-md-6">
                            <b-button
                                type="is-primary"
                                expanded
                                @click="onUploadDocuments"
                                :loading="loading"
                                :disabled="loading"
                            >
                                Загрузить
                            </b-button>
                        </div>
                        <div class="col-md-6">
                            <b-button
                                type="is-danger"
                                expanded
                                :loading="loading"
                                :disabled="loading"
                                @click="clearFiles"
                            >
                                Очистить
                            </b-button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="" v-else>
                <div class="row mb-2" v-for="task in details" @key="task.id">
                    <div class="col-md-6">
                        <img :src="'/storage/' + task.document_path" alt="">
                    </div>
                    <div class="col-md-6">
                        <h2 class="subtitle">{{ getDocumentNameByKey(task.document_type) }}</h2>
                        <div v-if="individualDocumentTypes.includes(task.document_type)">
                            <p>
                                <b>У данного физического лица уже существует документ такого типа!<br>
                                    Заменить его на новый экземпляр?</b>
                            </p>
                            <b-button
                                type="is-warning"
                                @click="dontChange(task.id)"
                                :disabled="tasksLoading[task.id].loading"
                            >
                                Не заменять
                            </b-button>
                            <b-button
                                type="is-danger"
                                @click="replaceDocument(task.id, task.document_type)"
                                :loading="tasksLoading[task.id].loading"
                            >
                                Заменить на новый!
                            </b-button>
                        </div>
                        <div v-else-if="cannotBeRecognized(task.document_type)">
                            <p class="text-danger">
                                <b>Документ не может быть распознан!</b>
                            </p>
                        </div>
                        <div v-else-if="canBeRecognized(task.document_type)">
                            <p class="text-success mb-3">
                                <b>Физическое лицо не имеет документа такого типа, добавить?</b>
                            </p>
                            <b-button
                                type="is-danger"
                                @click="dontChange(task.id)"
                                :disabled="tasksLoading[task.id].loading"
                            >
                                Отмена
                            </b-button>
                            <b-button
                                type="is-success"
                                @click="addDocument(task.id)"
                                :loading="tasksLoading[task.id].loading"
                            >
                                Добавить
                            </b-button>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <div class="col-md-3">
                        <b-button
                            type="is-success"
                            @click="endUploadingDocuments"
                            expanded
                        >
                            Готово!
                        </b-button>
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
import documentService from "../../services/document/documentService";
import EventBus from "../../events/eventBus";
export default {
    name: "IndividualPage",
    components: {
        DefaultLayout
    },
    mixins: [individualsMixin],
    props: ['id'],
    data: () => ({
        individual: [],
        availableTypes: [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'application/pdf',
            'image/bmp',
            'image/tiff',
            'image/gif',
            'image/vnd.djvu',
            'image/djvu'
        ],
        documentFiles: [],
        fileNames: [],
        filesPreviews: [],
        dropzoneActive: false,
        receivedDetails: false,
        details: [],
        loading: false,
        tasksLoading: {}
    }),
    async mounted() {
        try {
            this.individual = await individualService.getIndividualUserById(this.id);
        } catch (error) {
            console.log(error);
        }
    },
    watch: {
        details() {
            const result = {};
            this.details.map(task => {
                if (!result[task.id]) result[task.id] = {};
                result[task.id].loading = false;
            });
            this.tasksLoading = result;
        }
    },
    methods: {
        getLevelOfConfidence(confidence) {
            if (confidence >= 0 && confidence < 0.49) return 'low';
            if (confidence >= 0.49 && confidence < 0.7) return 'middle';
            if (confidence > 0.7) return 'high';
        },
        onDrop(event) {
            this.addFiles(event.dataTransfer.files);
        },
        clearFiles() {
            this.filesPreviews = [];
            this.fileNames = [];
            this.documentFiles = [];
            this.dropzoneActive = false;
        },
        onDropzoneHover() {
            this.dropzoneActive = true;
        },
        onDropzoneLeave() {
            this.dropzoneActive = false;
        },
        onChange(event) {
            this.addFiles(event.target.files);
            window.scrollTo(0,document.body.scrollHeight);
        },
        dontChange(taskId) {
            const index = this.details.findIndex(item => item.id === taskId);
            this.details = [
                ...this.details.slice(0, index),
                ...this.details.slice(index + 1)
            ];
            if (!this.details.length) this.endUploadingDocuments();
        },
        addFiles(files) {
            [...files].map(file => {
                if(file.size < this.maxFileSize) {
                    if (this.availableTypes.includes(file.type)) {
                        if (!this.fileNames.includes(file.name)) {
                            this.documentFiles.push(file);
                        }
                    }
                }
            });
            this.makePreviews();
        },
        makePreviews() {
            const filesPreviews = [];
            this.documentFiles.map(file => {
                filesPreviews.push(file.name);
            });
            this.filesPreviews = filesPreviews;
        },
        endUploadingDocuments() {
            this.clearFiles();
            this.receivedDetails = false;
        },
        makeTaskLoading(taskId, value) {
            this.tasksLoading = {
                ...this.tasksLoading,
                [taskId]: {
                    ...this.tasksLoading,
                    loading: value
                }
            };
        },
        async onUploadDocuments() {
            if (this.documentFiles.length) {
                try {
                    this.loading = true;
                    const formData = new FormData();
                    this.documentFiles.map(file => {
                        formData.append('documents[]', file);
                    });
                    this.details = await documentService.classifyDocuments(formData);
                    this.receivedDetails = true;
                    this.loading = false;
                } catch (error) {
                    this.loading = false;
                    EventBus.$emit('error', error.message);
                }
            }
        },
        async replaceDocument(taskId, docType) {
            try {
                let documentId = "";
                this.individual.documents.map(document => {
                    if (document.type === docType) {
                        documentId = document.id;
                    }
                });
                this.makeTaskLoading(taskId, true);
                await documentService.replaceDocument({
                    task_id: taskId,
                    document_id: documentId
                });
                EventBus.$emit('success', 'Документ был успешно заменен!');
                this.makeTaskLoading(taskId, false);
                this.dontChange(taskId);
                this.individual = await individualService.getIndividualUserById(this.id);
            } catch (error) {
                this.makeTaskLoading(taskId, false);
                EventBus.$emit('error', error.message);
            }
        },
        async addDocument(taskId) {
            try {
                this.makeTaskLoading(taskId, true);
                await documentService.saveDocumentForIndividual({
                    task_id: taskId,
                    individual_id: this.individual.id
                });
                this.makeTaskLoading(taskId, false);
                this.individual = await individualService.getIndividualUserById(this.id);
                this.dontChange(taskId);
                EventBus.$emit('success', 'Документ был успешно добавлен!');
            } catch (error) {
                this.makeTaskLoading(taskId, false);
                EventBus.$emit('error', error.message);
            }
        }
    },
    computed: {
        maxFileSize() {
            return 10000000; // 10 MB
        },
        individualDocumentTypes() {
            return this.individual.documents.map(document => document.type);
        }
    },
}
</script>

<style scoped>
.file-preview {
    background: lightblue;
    border: 1px solid blue;
    padding: 10px;
    border-radius: 40px;
}
</style>
