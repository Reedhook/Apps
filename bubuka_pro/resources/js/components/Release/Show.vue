<script>
import api from "../../api.js";

export default {
    name: "Show",
    data() {
        return {
            release: null,
            formOpen: false,

        }
    },
    methods: {
        deleteDate(id) {
            api.delete(`/api/releases/${id}`)
                .then(res => {
                    this.$router.push({name: 'release.index'});
                })
        },
        getRelease() {
            api.get(`/api/releases/${this.$route.params.id}`)
                .then(res => {
                    this.release = res.data.body.release;
                    if (this.release.is_ready === 1) {
                        this.release.is_ready = 'да'
                    } else {
                        this.release.is_ready = 'нет'
                    }
                    if (this.release.is_public === 1) {
                        this.release.is_public = 'да'
                    } else {
                        this.release.is_public = 'нет'
                    }
                    this.getFile();
                    this.getProject();
                    this.getPlatform();
                    this.getChangelog();
                    this.getReleaseType();
                    this.getTechnicalRequirement();
                })
        },
        getProject() {
            api.get(`/api/projects/${this.release.project_id}`)
                .then(res => {
                    this.release.project_id = res.data.body.project

                })
        },
        getPlatform() {
            api.get(`/api/platforms/${this.release.platform_id}`)
                .then(res => {
                    this.release.platform_id = res.data.body.platform
                })
        },
        getFile() {
            api.get(`/api/files/${this.release.file_id}`)
                .then(res => {
                    this.release.file_id = res.data.body.file
                })
        },
        getReleaseType() {
            api.get(`/api/releases_types/${this.release.release_type_id}`)
                .then(res => {
                    this.release.release_type_id = res.data.body.release_type
                })
        },
        getTechnicalRequirement() {
            api.get(`/api/techs_reqs/${this.release.technical_requirement_id}`)
                .then(res => {
                    this.release.technical_requirement_id = res.data.body.technical_requirement
                })
        },
        getChangelog() {
            api.get(`/api/changes/${this.release.change_id}`)
                .then(res => {
                    this.release.change_id = res.data.body.changelog
                })
        },
        download() {
            api.get(this.release.download_url, {responseType: 'blob'})
                .then(res => {
                    const url = window.URL.createObjectURL(new Blob([res.data]));
                    const link = document.createElement('a');
                    link.href = url;

                    // Получаем имя файла из заголовков ответа
                    const contentDisposition = res.headers['content-disposition'];
                    const filename = contentDisposition.split(';')[1].trim().split('=')[1];

                    // Декодируем имя файла из URI-кодировки
                    const decodedFilename = decodeURIComponent(escape(filename));

                    link.setAttribute('download', decodedFilename); // Используем правильное имя файла
                    document.body.appendChild(link);
                    link.click();
                    window.URL.revokeObjectURL(url);
                })
                .catch(error => {
                    console.log(error);
                });
        }
    },
    created() {
        this.getRelease();
    },
}
</script>

<template>
    <div class="w-75" v-if="release">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Version</th>
                <th scope="col">Project name</th>
                <th scope="col">Platform name</th>
                <th scope="col">Release Type</th>
                <th scope="col">OS type</th>
                <th scope="col">Specifications</th>
                <th scope="col">News</th>
                <th scope="col">Changes</th>
                <th scope="col">Можно скачивать</th>
                <th scope="col">Разрешение на релиз</th>
                <th scope="col">Description</th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ release.id }}</td>
                <td> {{ release.version }}</td>
                <td> {{ release.project_id.name }}</td>
                <td> {{ release.platform_id.name }}</td>
                <td> {{ release.release_type_id.name }}</td>
                <td> {{ release.technical_requirement_id.os_type }}</td>
                <td> {{ release.technical_requirement_id.specifications }}</td>
                <td> {{ release.change_id.news }}</td>
                <td> {{ release.change_id.changes }}</td>
                <td> {{ release.is_ready }}</td>
                <td> {{ release.is_public }}</td>
                <td> {{ release.description }}</td>
                <td>
                    <div class="btn icons" @click="download" id="download">
                        <i class="gg-software-download"></i>
                    </div>
                </td>
                <td>
                    <template v-if="formOpen">
                        <div class="btn icons" id="edit">
                            <i class="gg-pen"></i>
                        </div>
                    </template>
                    <template v-else>
                        <router-link :to="{name:'release.edit', params:{id:release.id}}" id="edit" class="btn"><i
                            class="gg-pen"></i></router-link>
                    </template>
                </td>
                <td>
                    <template v-if="formOpen">
                        <div class="btn icons">
                            <i class="gg-trash"></i>
                        </div>
                    </template>
                    <template v-else>
                        <a @click.prevent="deleteDate(release.id)" href="#" id="trash" class="btn"><i
                            class="gg-trash"></i></a>
                    </template>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</template>

<style scoped>
/* Общие стили таблицы */
.table {
    width: 100%;
    border-collapse: collapse;
    border-spacing: 0;
}

/* Стили для заголовков таблицы */
th {
    background-color: #f2f2f2;
    color: #333;
    font-weight: bold;
    padding: 10px;
    text-align: left;
}

/* Стили для четных строк таблицы */
tr:nth-child(even) {
    background-color: #f9f9f9;
}

/* Стили для нечетных строк таблицы */
tr:nth-child(odd) {
    background-color: #ffffff;
}

/* Стили для ячеек таблицы */
td {
    padding: 10px;
    border: 1px solid #ddd;
}

</style>
