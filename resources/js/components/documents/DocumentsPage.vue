<template>
    <DefaultLayout>
        <template v-slot:title>
            Загрузка документов
        </template>
        <template v-slot:content>
            <b-steps
                v-model="activeStep"
                :has-navigation="false"
            >
                <b-step-item
                    label="Загрузка документов"
                    step="1"
                    :clickable="false"
                    class="mt-4"
                >
                    <UploadDocuments @upload-documents="uploadDocuments" />
                </b-step-item>
                <b-step-item
                    label="Обработка"
                    step="2"
                    :clickable="false"
                    class="mt-4"
                >
                    <ClassificationComponent
                        :details="classifiedDetails"
                        @recognize="onRecognize"
                    />
                </b-step-item>
            </b-steps>
        </template>
    </DefaultLayout>
</template>

<script>
import DefaultLayout from "../layouts/DefaultLayout";
import documentService from "../../services/document/documentService";
import EventBus from "../../events/eventBus";
import UploadDocuments from "./UploadDocuments";
import ClassificationComponent from "../classification/ClassificationComponent";
import roleMixin from "../../mixins/roleMixin";
export default {
    name: "DocumentsPage",
    data: () => ({
        activeStep: 0,
        uploadLoading: false,
        classifiedDetails: []
    }),
    mixins: [roleMixin],
    components: {
        DefaultLayout,
        UploadDocuments,
        ClassificationComponent
    },
    methods: {
        async uploadDocuments(files) {
            try {
                this.uploadLoading = true;
                const formData = new FormData();
                files.map(file => {
                    formData.append('documents[]', file);
                });
                const response = await documentService.classifyDocuments(formData);
                this.classifiedDetails = _.groupBy(response, 'task_id');
                this.uploadLoading = false;
                this.activeStep = 1;
            } catch (error) {
                this.uploadLoading = false;
                EventBus.$emit('error', error.message);
            }
        },
        async onRecognize(taskId) {
            try {
                console.log(taskId);
                await documentService.recognizeTask(taskId);
            } catch (error) {
                EventBus.$emit('error', error.message);
            }
        }
    },
}
</script>

<style scoped lang="scss">
.dropzone {
    border: 8px dashed #aaa;
    box-sizing: border-box;
    border-radius: 156px;
    height: 264px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    cursor: pointer;
    color: #aaa;
    margin-bottom: 24px;
    position: relative;
    transition: all .5s ease;
    user-select: none;
    &:hover {
        border: 8px dashed #000;
        color: #000;
        transition: all .5s ease;
    }
    &-active {
        border: 8px dashed #000;
        color: #000;
    }
    &-title {
        font-weight: 500;
        font-size: 36px;
        line-height: 42px;
        margin-bottom: 12px;
    }
    &-subtitle {
        font-weight: 500;
        font-size: 16px;
        line-height: 19px;
    }
}
.file-preview {
    background: lightblue;
    border: 1px solid blue;
    padding: 10px;
    border-radius: 40px;
}
</style>
