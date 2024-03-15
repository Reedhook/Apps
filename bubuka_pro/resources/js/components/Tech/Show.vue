<script>
import api from "../../api.js";

export default {
    name: "Show",
    data() {
        return {
            tech: null
        }
    },
    methods: {
        deleteDate(id) {
            api.delete(`/api/techs_reqs/${id}`)
                .then(res => {
                    this.getData()
                })
        },
        getDate() {
            api.get(`/api/techs_reqs/${this.$route.params.id}`)
                .then(res => {
                        this.tech = res.data.body.technical_requirement
                    }
                )
        }
    },
    mounted() {
        this.getDate();
    }

}
</script>

<template>
    <div class="w-50" v-if="tech">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Operation system type</th>
                <th scope="col">Specifications</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ tech.id }}</td>
                <td> {{ tech.os_type}}</td>
                <td> {{ tech.specifications}}</td>
                <td>
                    <router-link :to="{name:'tech.edit', params:{id:tech.id}}" id="edit" class="btn"><i class="gg-pen"></i></router-link>
                </td>
                <td>
                    <a @click.prevent="deleteDate(tech.id)" href="#" id="trash" class="btn"><i class="gg-trash"></i></a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</template>

<style scoped>

</style>
