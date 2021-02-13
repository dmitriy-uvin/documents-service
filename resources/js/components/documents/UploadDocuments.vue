<template>
    <div class="multiple-upload">
        <label class="dropzone"
               :class="{ 'dropzone-active' : dropzoneActive }"
               @drop.prevent="onDrop"
               @dragover.prevent="onDropzoneHover"
               @dragleave.prevent="onDropzoneLeave"
               for="file-uploader"
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
        >
        <div class="previews" v-if="imagesPreviews.length || filesPreviews.length">
            <div
                class="files-previews d-flex justify-content-center mb-3"
                v-if="filesPreviews.length"
            >
                <div class="file-preview mr-2" v-for="filePreview in filesPreviews">
                    {{ filePreview }}
                </div>
            </div>
            <div class="images-previews d-flex justify-content-center">
                <figure v-if="imagesPreviews.length"
                        class="image mt-0 mb-0 mr-1"
                        v-for="imagePreview in imagesPreviews">
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
                        @click="onUploadDocuments"
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
    </div>
</template>

<script>
export default {
    name: "UploadDocuments",
    data: () => ({
        dropFiles: [],
        uploadLoading: false,
        dropzoneActive: false,
        imagesPreviews: [],
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
        filesPreviews: [],
        filesNames: []
    }),
    methods: {
        clearFiles() {
            this.dropFiles = [];
            this.imagesPreviews = [];
            this.filesPreviews = [];
            this.dropzoneActive = false;
        },
        onChange(event) {
            this.addFiles(event.target.files);
        },
        addFiles(files) {
            [...files].map(file => {
                if(file.size < this.maxFileSize) {
                    if (this.availableTypes.includes(file.type)) {
                        if (!this.filesNames.includes(file.name)) {
                            this.dropFiles.push(file);
                        }
                    }
                }
            });
            this.makePreviews();
        },
        onDropzoneHover() {
            this.dropzoneActive = true;
        },
        onDropzoneLeave() {
            this.dropzoneActive = false;
        },
        onDrop(event) {
            this.addFiles(event.dataTransfer.files);
        },
        makePreviews() {
            const previews = [];
            const filesPreviews = [];
            this.dropFiles.map(file => {
                const reader = new FileReader();
                if (file.type.includes('image/') && (!file.type.includes('tiff') && !file.type.includes('djvu'))) {
                    reader.readAsDataURL(file);
                    reader.onloadend = function() {
                        previews.push(reader.result);
                    }
                } else {
                    filesPreviews.push(file.name);
                }
            });
            this.imagesPreviews = previews;
            this.filesPreviews = filesPreviews;
        },
        onUploadDocuments() {
            if (this.dropFiles.length) {
                this.$emit('upload-documents', this.dropFiles);
            }
        }
    },
    computed: {
        maxFileSize() {
            return 10000000; // 10 MB
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
