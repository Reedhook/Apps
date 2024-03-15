<script>
import api from '../../api.js';

export default {
    name: "Index",
    data() {
        return {
            releases: null
        }
    },
    methods: {
        getProjects() {
            api.get('/api/releases/')
                .then(res => {
                    this.releases = res.data.body.releases
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
                <th scope="col"><router-link :to="{name:'release.create'}" id="add_date"><i class="m-lg-2 gg-add-r"></i></router-link></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(release, i) in releases">
                <td>{{ i + 1 }}</td>
                <td>
                    <h4>{{ release.version }}</h4>
                </td>
                <td>
                    <div>
                        <router-link :to="{name:'release.show', params:{id:release.id}}" id="info" class="btn"><i
                            class="gg-file-document"></i></router-link>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>
