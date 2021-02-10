<template>
    <div class="card">
        <div class="card-header d-block">
            <h2 class="title is-4 text-center py-3">
                Загрузка документов
            </h2>
        </div>
        <div class="card-content">
            <div class="content">
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

                <div class="buttons">

                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "DocumentsPage",
    data: () => ({
        dropFiles: []
    }),
    methods: {
        deleteDropFile(index) {
            this.dropFiles.splice(index, 1);
        }
    }
}
</script>

<style scoped>

</style>
