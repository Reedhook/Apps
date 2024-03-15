<script>
import api from "../../../api.js";
import EditPlatform from "./EditPlatform.vue";
import EditChangelog from "./EditChangelog.vue";
import EditReleaseType from "./EditReleaseType.vue";
import EditTechnicalRequirement from "./EditTechnicalRequirement.vue";
import EditVersion from "./EditVersion.vue";
import EditFile from "./EditFile.vue";

export default {
    name: "Edit",
    components: {
        EditFile,
        EditVersion,
        EditTechnicalRequirement,
        EditReleaseType,
        EditChangelog,
        EditPlatform,
    },
    data() {
        return {
            release: null,
            selectedPlatform: null,
            selectedProject: null,
            selectedChangelog: null,
            selectedReleaseType: null,
            selectedTechnicalRequirement: null,
            project: null,
            version: null,
            platform_id: null,
            release_type_id: null,
            technical_requirement_id: null,
            change_id: null,
            description: null,
            is_ready: null,
            file: null,
        }
    },
    methods: {
        openForm(id) {
            const form = document.querySelector(id);
            if (form.classList.contains('open')) {
                // Если форма уже открыта, закрываем ее
                form.classList.remove('open');
                this.formOpen = false;
            } else {
                // Закрываем все другие формы
                document.querySelectorAll('.open').forEach((otherForm) => {
                    otherForm.classList.remove('open');
                });

                // Открываем форму с переданным id
                form.classList.add('open');
                this.formOpen = true;
            }
        },
        getRelease() {
            api.get(`/api/releases/${this.$route.params.id}`)
                .then(res => {
                    this.release = res.data.body.release;
                    this.getProject(res.data.body.release.project_id);
                    this.selectedPlatform = res.data.body.release.platform_id;
                    this.selectedProject = res.data.body.release.project_id;
                    this.selectedChangelog = res.data.body.release.change_id;
                    this.selectedReleaseType = res.data.body.release.release_type_id;
                    this.selectedTechnicalRequirement = res.data.body.release.technical_requirement_id
                    this.version = res.data.body.release.version;
                    this.description = res.data.body.release.description;
                })
        },
        getProject(uri) {
            api.get(`/api/projects/${uri}`)
                .then(res => {
                    this.project = res.data.body.project;
                })
        },
        update() {
            const formData = new FormData();
            if (this.file != null) {
                formData.append('file', this.file);
            }
            formData.append('platform_id', this.selectedPlatform);
            formData.append('change_id', this.selectedChangelog);
            formData.append('release_type_id', this.selectedReleaseType);
            formData.append('technical_requirement_id', this.selectedTechnicalRequirement);
            if (this.description != null) {
                formData.append('description', this.description);
            }
            if (this.is_ready != null) {
                formData.append('is_ready', this.is_ready);
            }
            if (this.version != null) {
                formData.append('version', this.version);
            }
            console.log(formData);
            api.post(`/api/releases/${this.$route.params.id}`, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(res => {
                this.$router.push({name: 'release.show', params: {id: res.data.id}});
                console.log(res.data);
            })
                .catch(error => {
                    console.error(error);
                });
        },
    },
    mounted() {
        this.getRelease()
    },
    computed: {
        isDisabled() {
            return this.name || this.description
        }
    }
}
</script>

<template>
    <div class="customForm">
        <div class="row mb-3">
            <h3>Release</h3>
        </div>
        <div class="w-25 customBorder p-4">
            <EditFile></EditFile>
            <EditPlatform></EditPlatform>
            <EditChangelog ref="changelogs"></EditChangelog>
            <EditReleaseType ref="release_types"></EditReleaseType>
            <EditTechnicalRequirement ref="technicals_requirements"></EditTechnicalRequirement>
            <div class="mb-3">
                <input v-model="description" type="text" class="form-control mb-3" placeholder="description">
            </div>
            <EditVersion></EditVersion>
            <div class="form-check form-switch">
                <input v-model="is_ready" class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
                <label class="form-check-label" for="flexSwitchCheckChecked">Ready for Download</label>
            </div>

            <i class="fa-regular fa-lock"></i>
            <input @click.prevent="update" type="submit" value="Add" class="btn btn-primary">
        </div>
    </div>
</template>

<style scoped>
/deep/ #project_list {
    display: block;
    padding: 10px 0;
    margin: 0;
}

/deep/ select {
    border: none; /* Убираем границу у выпадающего списка */
    background-color: transparent; /* Устанавливаем прозрачный фон */
    outline: none; /* Убираем обводку при фокусе */
}

/deep/ .select-list {
    width: 100%; /* Ширина списка равна 100% */
    padding: 10px; /* Внутренний отступ */
    margin: 0; /* Убираем внешние отступы */
    text-align: center; /* Выравниваем текст по центру */
}

/deep/ .table {
    margin: 0 auto; /* Центрируем таблицу по горизонтали */
}

/deep/ .selectedModel {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    margin-bottom: 10px;
}

/deep/ .selectedProject p {
    margin: 0;
    font-weight: bold;
}

/deep/ .selectedProject p:first-child {
    margin-bottom: 5px;
}

/deep/ .selected {
    background-color: #3498db; /* Цвет подсветки для выбранных строк */
    color: #fff; /* Цвет текста для выбранных строк */
}

/deep/ .formFields {
    display: flex;
    align-items: center;
    margin-bottom: 3rem; /* или любой другой отступ по вашему выбору */
}
</style>
