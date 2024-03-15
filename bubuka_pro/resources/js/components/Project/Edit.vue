<script>
import api from "../../api.js";
export default {
    name: "Edit",
    data() {
        return {
            name: null,
            description: null
        }
    },
    methods: {
        getProject() {
            api.get(`/api/projects/${this.$route.params.id}`)
                .then(res => {
                    this.name = res.data.body.project.name
                    this.description = res.data.body.project.description
                    }
                )
        },
        update(){
            api.patch(`/api/projects/${this.$route.params.id}`, {name: this.name})
                .then(res=>{
                    this.$router.push({name:'project.show', params: {id: this.$route.params.id}});
                })
        }
    },
    mounted() {
        this.getProject()
    },
    computed:{
        isDisabled(){
            return this.name || this.description
        }
    }
}
</script>

<template>
    <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <div class="row mb-3">
            <h3>Project</h3>
        </div>
        <div class="w-25 border border rounded p-4">
            <div class="mb-3">
                <input type="text" v-model="name" placeholder="name" class="form-control">
            </div>
            <div class="mb-3">
                <input type="text" v-model="description" placeholder="description" class="form-control">
            </div>
            <div class="mb-3">
                <input :disabled="!isDisabled" @click.prevent="update" type="submit" value="Update" class="btn btn-primary">
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
