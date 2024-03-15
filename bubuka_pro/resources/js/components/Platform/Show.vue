<script>
import api from "../../api.js";

export default {
    name: "Show",
    data() {
        return {
            platform: null
        }
    },
    methods: {
        deleteDate(id) {
            api.delete(`/api/platforms/${id}`)
                .then(res => {
                    this.getPlatforms()
                })
        },
        getPlatform() {
            api.get(`/api/platforms/${this.$route.params.id}`)
                .then(res => {
                        this.platform = res.data.body.platform
                    }
                )
        },
    },
    mounted() {
       this.getPlatform();
    }

}
</script>

<template>
    <div class="w-25" v-if="platform">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Name</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ platform.id }}</td>
                <td> {{ platform.name }}
                </td>
                <td>
                    <router-link :to="{name:'platform.edit', params:{id:platform.id}}" id="edit" class="btn"><i class="gg-pen"></i></router-link>
                </td>
                <td>
                    <a @click.prevent="deleteDate(platform.id)" href="#" id="trash" class="btn"><i class="gg-trash"></i></a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</template>

<style scoped>

</style>
