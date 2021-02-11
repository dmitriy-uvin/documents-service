<template>
    <DefaultLayout>
        <template v-slot:title>
            Загрузка документов
        </template>
        <template v-slot:content>
            <b-field class="d-flex justify-content-center">
                <b-upload v-model="dropFiles"
                          multiple expanded
                          drag-drop class="d-flex">
                    <section class="section">
                        <div class="content has-text-centered">
                            <p>
                                <b-icon
                                    icon="upload"
                                    size="is-large">
                                </b-icon>
                            </p>
                            <p>Выберите файлы для распознавания</p>
                        </div>
                    </section>
                </b-upload>
            </b-field>
            <div class="d-flex justify-content-center">
                <div class="tags d-flex">
                    <span v-for="(file, index) in dropFiles"
                          :key="index"
                          class="tag is-primary"
                    >
                        {{ file.name }}
                        <button class="delete is-small"
                                type="button"
                                @click="deleteDropFile(index)">
                        </button>
                    </span>
                </div>
            </div>
            <div class="buttons d-flex justify-content-center">
                <div class="col-md-3">
                    <b-button type="is-primary" expanded>
                        Загрузить
                    </b-button>
                </div>
            </div>
        </template>
    </DefaultLayout>
</template>

<script>
import DefaultLayout from "../layouts/DefaultLayout";
export default {
    name: "DocumentsPage",
    data: () => ({
        dropFiles: []
    }),
    components: {
        DefaultLayout,
    },
    methods: {
        deleteDropFile(index) {
            this.dropFiles.splice(index, 1);
        }
    }
}
</script>

<style scoped>

</style>
