<script>
import api from '../../api.js';

export default {
    name: "Index",
    data() {
        return {
            platforms: null
        }
    },
    methods: {
        getPlatforms() {
            api.get('/api/platforms')
                .then(res => {
                    this.platforms = res.data.body.platforms
                })
        }

    },
    mounted() {
        this.getPlatforms()
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
            <tr v-for="(platform, i) in platforms">
                <td>{{ i + 1 }}</td>
                <td>
                    <h4>{{platform.name}}</h4>
                </td>
                <td>
                    <router-link :to="{name:'platform.show', params:{id:platform.id}}" id="info"><i class="gg-file-document"></i></router-link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>
