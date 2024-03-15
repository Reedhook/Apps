<script>
import api from '../../api.js';

export default {
    name: "Index",
    data() {
        return {
            techs: null
        }
    },
    methods: {
        getData() {
            api.get('/api/techs_reqs/')
                .then(res => {
                    this.techs = res.data.body.technicals_requirements
                })
        }

    },
    mounted() {
        this.getData()
    }
}
</script>

<template>
    <div class="w-25">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Operation system type</th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(tech, i) in techs" style="vertical-align: top;">
                <td>{{ i + 1 }}</td>
                <td>
                    <h4>{{tech.os_type}}</h4>
                </td>
                <td>
                    <router-link :to="{name:'tech.show', params:{id:tech.id}}" id="info" class="btn"><i class="gg-file-document"></i></router-link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>
