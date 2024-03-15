<script>
import api from '../../api.js';

export default {
    name: "Index",
    data() {
        return {
            projects: null
        }
    },
    methods: {
        getProjects() {
            api.get('/api/projects/')
                .then(res => {
                    this.projects = res.data.body.projects
                })
        }

    },
    mounted() {
        this.getProjects()
    }
}
</script>

<template>
    <div class="w-25">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(project, i) in projects">
                <td>{{ i + 1 }}</td>
                <td>
                    <h4>{{project.name}}</h4>
                </td>
                <td>
                    <div>
                        <router-link :to="{name:'project.show', params:{id:project.id}}" id="info" class="btn"><i class="gg-file-document"></i></router-link>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>
