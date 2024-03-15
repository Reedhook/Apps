<script>
import api from '../../api.js';

export default {
    name: "Index",
    data() {
        return {
            releases_types: null
        }
    },
    methods: {
        getReleasesTypes() {
            api.get('/api/releases_types/')
                .then(res => {
                    this.releases_types = res.data.body.releases_types
                })
        }

    },
    mounted() {
        this.getReleasesTypes()
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
            <tr v-for="(release_type, i) in releases_types">
                <td>{{ i + 1 }}</td>
                <td>
                    {{release_type.name}}
                </td>
                <td>
                    <div>
                        <router-link :to="{name:'release_type.show', params:{id:release_type.id}}" id="info" class="btn"><i class="gg-file-document"></i></router-link>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>
