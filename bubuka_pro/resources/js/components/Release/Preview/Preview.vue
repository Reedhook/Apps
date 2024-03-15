<script>

import AddFile from "./AddFile.vue";
import api from "../../../api.js";

export default {
    name: "Preview",
    components: {AddFile},
    data() {
        return {
            data: null,
            file: null,
        }
    },
    methods: {
        store() {
            const formData = new FormData();
            formData.append('file', this.file);
            formData.append('project_id', this.data.project.id);
            formData.append('platform_id', this.data.platform.id);
            formData.append('change_id', this.data.changelog.id);
            formData.append('release_type_id', this.data.release_type.id);
            formData.append('technical_requirement_id', this.data.tech.id);
            formData.append('description', this.data.description);
            formData.append('is_ready', this.data.is_ready);
            formData.append('version', this.data.version);
            api.post('/api/releases/', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(res => {
                this.$router.push({name: 'release.index'})
                console.log(res.data);
            })
                .catch(error => {
                    console.error(error);
                });
        }
    },
    created() {
        this.data = JSON.parse(this.$route.query.data);
        console.log(this.data);
    },
}
</script>

<template>
    <div class="customForm">
        <div class="row mb-3">
        </div>
        <div class="w-25 customBorder p-4">
            <AddFile></AddFile>
            <div class="mb-3 selectedModel">
                <p v-if="this.data.project">
                    {{ this.data.project.name }}</p>
                <p v-else>Проект не выбран</p>
            </div>
            <div class="mb-3 selectedModel">
                <p v-if="this.data.platform">
                    {{ this.data.platform.name }}</p>
                <p v-else>Платформа не выбрана</p>
            </div>
            <div class="mb-3 selectedModel">
                <p v-if="this.data.changelog">
                    {{ this.data.changelog.news }}</p>
                <p v-else>Новостей нет</p>
            </div>
            <div class="mb-3 selectedModel">
                <p v-if="this.data.changelog">
                    {{ this.data.changelog.changes }}</p>
                <p v-else>Изменений нет</p>
            </div>
            <div class="mb-3 selectedModel">
                <p v-if="this.data.description">
                    {{ this.data.description }}</p>
                <p v-else>Описания нет</p>
            </div>
            <div class="mb-3 selectedModel">
                <p v-if="this.data.tech">
                    {{ this.data.tech.os_type }}</p>
                <p v-else>Нет типа операционной системы</p>
            </div>
            <div class="mb-3 selectedModel">
                <p v-if="this.data.tech">
                    {{ this.data.tech.specifications }}</p>
                <p v-else>Нет дополнительных характеристик</p>
            </div>
            <div class="mb-3 selectedModel">
                <p v-if="this.data.version">
                    {{ this.data.version }}</p>
                <p v-else>Нет версии</p>
            </div>
            <div class="mb-3 selectedModel">
                <p v-if="this.data.is_ready">
                    Готов к скачиванию:
                    {{ this.data.is_ready }}</p>
                <p v-else>false</p>
            </div>

            <i class="fa-regular fa-lock"></i>
            <input @click.prevent="store" type="submit" value="Add" class="btn btn-primary">
        </div>
    </div>
</template>

<style scoped>
/deep/ .selectedModel {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    margin-bottom: 10px;
}
</style>
