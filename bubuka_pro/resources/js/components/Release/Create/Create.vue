<script>
import api from "../../../api.js";
import AddProject from "./AddProject.vue";
import AddPlatform from "./AddPlatform.vue";
import AddChangelog from "./AddChangelog.vue";
import AddReleaseType from "./AddReleaseType.vue";
import AddTechnicalRequirement from "./AddTechnicalRequirement.vue";
import AddFile from "../Preview/AddFile.vue";
import AddVersion from "./AddVersion.vue";

export default {
    name: "Create",
    components: {AddVersion, AddFile, AddTechnicalRequirement, AddReleaseType, AddChangelog, AddPlatform, AddProject},
    data() {
        return {
            release: null,
            projects: null,
            description: null,
            version: null,
            selectedPlatform: null, // Массив для хранения выбранных платформ
            selectedProject: null,
            selectedChangelog: null,
            selectedReleaseType: null,
            selectedTechnicalRequirement: null,
            formOpen: false,
            isReady: false
        }
    },
    methods: {
        store() {
            const dataArray = {
                is_ready: this.isReady,
                version: this.version,
                description: this.description,
                project: this.projects.find(project =>project.id === this.selectedProject),
                platform: this.projects.find(project => project.id === this.selectedProject).platforms.find(platform => platform.id ===this. selectedPlatform),
                release_type: this.$refs.release_types.releases_types.find(release_type => release_type.id === this.selectedReleaseType),
                changelog: this.$refs.changelogs.changelogs.find(changelog =>changelog.id === this.selectedChangelog),
                tech: this.$refs.technicals_requirements.techs.find(tech => tech.id===this.selectedTechnicalRequirement)
            };

            const serializedData = JSON.stringify(dataArray);
            this.$router.push({name: 'release.preview', query: {data: serializedData}});
        },
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
    },
    computed: {
        isDisabled() {
            return this.selectedPlatform && this.selectedProject && this.selectedChangelog && this.selectedReleaseType && this.selectedTechnicalRequirement;
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
            <AddProject></AddProject>
            <AddPlatform></AddPlatform>
            <AddChangelog ref="changelogs"></AddChangelog>
            <AddReleaseType ref="release_types"></AddReleaseType>
            <AddTechnicalRequirement ref="technicals_requirements"></AddTechnicalRequirement>
            <div class="mb-3">
                <input v-model="description" type="text" class="form-control mb-3" placeholder="description">
            </div>
            <AddVersion></AddVersion>
            <div class="form-check form-switch">
                <input v-model="isReady" class="form-check-input" type="checkbox" id="flexSwitchCheckChecked" checked>
                <label class="form-check-label" for="flexSwitchCheckChecked">Ready for Download</label>
            </div>

                <i class="fa-regular fa-lock"></i>
                <input @click.prevent="store" type="submit" value="Add" class="btn btn-primary">
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
