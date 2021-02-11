<template>
    <DefaultLayout>
        <template v-slot:title>
            Загрузка документов
        </template>

        <template v-slot:content>
<!--            <b-field class="d-flex justify-content-center">-->
<!--                <b-upload v-model="dropFiles"-->
<!--                          multiple expanded-->
<!--                          drag-drop class="d-flex"-->
<!--                >-->
<!--                    <section class="section">-->
<!--                        <div class="content has-text-centered">-->
<!--                            <p>-->
<!--                                <b-icon-->
<!--                                    icon="upload"-->
<!--                                    size="is-large">-->
<!--                                </b-icon>-->
<!--                            </p>-->
<!--                            <p>Выберите файлы для распознавания</p>-->
<!--                        </div>-->
<!--                    </section>-->
<!--                </b-upload>-->
<!--            </b-field>-->
<!--            <div class="d-flex justify-content-center">-->
<!--                <div class="tags d-flex">-->
<!--                    <span v-for="(file, index) in dropFiles"-->
<!--                          :key="index"-->
<!--                          class="tag is-primary"-->
<!--                    >-->
<!--                        {{ file.name }}-->
<!--                        <button class="delete is-small"-->
<!--                                type="button"-->
<!--                                @click="deleteDropFile(index)">-->
<!--                        </button>-->
<!--                    </span>-->
<!--                </div>-->
<!--            </div>-->
            <div class="multiple-upload">
                <div class="dropzone"
                    :class="{ 'dropzone-active' : dropzoneActive }"
                    @drop.prevent="onDrop"
                    @dragover.prevent="onDropzoneHover"
                    @dragleave.prevent="onDropzoneLeave"
                >
                    <div class="dropzone-title">
                        Выберите файлы для распознавания
                    </div>
                    <div class="dropzone-subtitle">или просто перетащите их сюда</div>
                </div>
                    <input
                        id="file-uploader"
                        accept="image/jpeg, image/png, .bmp, .tiff, .gif, .djvu, .pdf"
                        multiple=""
                        type="file"
                        autocomplete="off"
                        tabindex="-1"
                        style="display: none;"
                        v-on:change="onChange"
                    >
                <div class="previews d-flex justify-content-center" v-if="imagesPreviews.length">
                    <figure class="image mt-0 mb-0" v-for="imagePreview in imagesPreviews">
                        <img :src="imagePreview" class="image-256x256" alt="imagePreview" />
                    </figure>
                </div>
            </div>
            <div class="buttons d-flex justify-content-center">
                <div
                    class="mt-3 row"
                    :class="{ 'col-md-6': dropFiles.length, 'col-md-3': !dropFiles.length }"
                >
                    <div
                        class="col-md-6"
                        :class="{ 'col-md-6': dropFiles.length, 'col-md-12': !dropFiles.length }"
                    >
                        <b-button
                            type="is-primary"
                            expanded
                            @click="uploadDocuments"
                            :loading="uploadLoading"
                        >
                            Загрузить
                        </b-button>
                    </div>
                    <div class="col-md-6" v-if="dropFiles.length">
                        <b-button
                            type="is-danger"
                            expanded
                            @click="clearFiles"
                        >
                            Очистить
                        </b-button>
                    </div>
                </div>
            </div>
        </template>
    </DefaultLayout>
</template>

<script>
import DefaultLayout from "../layouts/DefaultLayout";
import documentService from "../../services/document/documentService";
import EventBus from "../../events/eventBus";
export default {
    name: "DocumentsPage",
    data: () => ({
        dropFiles: [],
        uploadLoading: false,
        dropzoneActive: false,
        imagesPreviews: [],
        availableTypes: [
            'image/jpeg',
            'image/png',
            'application/pdf',
            '.bmp',
            '.tiff',
            '.gif',
            '.djvu'
        ]
    }),
    components: {
        DefaultLayout,
    },
    methods: {
        deleteDropFile(index) {
            this.dropFiles.splice(index, 1);
        },
        clearFiles() {
            this.dropFiles = [];
            this.imagesPreviews = [];
            this.dropzoneActive = false;
        },
        onChange(event) {
            this.addFiles(event.target.files);
            this.makePreviews(event.target.files);
        },
        addFiles(files) {
            this.dropFiles.push(...files);
        },
        async uploadDocuments() {
            try {
                this.uploadLoading = true;
                const formData = new FormData();
                this.dropFiles.map(file => {
                    formData.append('documents[]', file);
                });
                console.log(this.dropFiles);
                // await documentService.uploadDocuments(formData);
                this.uploadLoading = false;
            } catch (error) {
                this.uploadLoading = false;
                console.log(error);
                EventBus.$emit('error', error.message);
            }
        },
        onDropzoneHover() {
            this.dropzoneActive = true;
        },
        onDropzoneLeave() {
            this.dropzoneActive = false;
        },
        onDrop(event) {
            this.addFiles(event.dataTransfer.files)
            this.makePreviews(event.dataTransfer.files);
        },
        makePreviews(files) {
            const previews = [
                ...this.imagesPreviews
            ];
            [...files].map(file => {
                const reader = new FileReader();
                if (file.type === 'image/jpeg' || file.type === 'image/png') {
                    reader.readAsDataURL(file);
                    reader.onloadend = function() {
                        previews.push(reader.result);
                    }
                }
            });
            this.imagesPreviews = previews;
        }
    }
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
</style>
