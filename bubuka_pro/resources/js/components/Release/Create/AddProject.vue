<script>
import api from "../../../api.js";

export default {
    name: "AddProject",
    methods: {
        showData(id) {
            this.getProjects();
            this.$parent.openForm(id);
        },
        getProjects() {
            api.get(`/api/projects/`)
                .then(res => {
                    this.$parent.projects = res.data.body.projects
                })
        },
        selectProject(id) {
            if (this.$parent.selectedProject === id) {
                this.$parent.selectedProject = null;
            } else {
                this.$parent.selectedProject = id;
            }
        },
    }
}
</script>

<template>
    <div class="mb-3">
        Выберите проект:
        <button id="add_Project" @click="this.showData('#addProject')" class="btn"><i class="gg-add"></i>
        </button>
        <div class="selectedModel">
            <p v-if="this.$parent.selectedProject">
                {{ this.$parent.projects.find(project => project.id === this.$parent.selectedProject).name }}</p>
            <p v-else>Проект</p>
        </div>
        <div id="addProject" class="showForm">
            <form role="form" action="/requestFine" autocomplete="off" method="POST">
                <ul>

                    <li id="project_list" v-for="project in this.$parent.projects" :key="project.id"
                        class="btn"
                        :class="{ 'selected':  this.$parent.selectedProject === project.id  }"
                        @click="this.selectProject(project.id)">
                        {{ project.name }}

                    </li>
                </ul>
                <button type="button" class="btn btn-danger" @click="this.$parent.openForm('#addProject')">
                    Закрыть
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped>

</style>
