<script>
import api from "../../api.js";

export default {
    name: "Show",
    data() {
        return {
            release_type: null
        }
    },
    methods: {
        deleteDate(id) {
            api.delete(`/api/releases_types/${id}`)
                .then(res => {
                    this.$router.push({name: 'release_type.index'})
                })
        },
        getReleaseType() {
            api.get(`/api/releases_types/${this.$route.params.id}`)
                .then(res => {
                        this.release_type = res.data.body.release_type
                    }
                )
        },
    },
    mounted() {
       this.getReleaseType();
    }

}
</script>

<template>
    <div class="w-25" v-if="release_type">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ release_type.id }}</td>
                <td> {{ release_type.name }}</td>
                <td> {{ release_type.description }}</td>
                <td>
                    <router-link :to="{name:'release_type.edit', params:{id:release_type.id}}" id="edit" class="btn"><i class="gg-pen"></i></router-link>
                </td>
                <td>
                    <a @click.prevent="deleteDate(release_type.id)" href="#" id="trash" class="btn"><i class="gg-trash"></i></a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</template>

<style scoped>

</style>
