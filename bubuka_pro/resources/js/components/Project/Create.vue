<script>
import api from '../../api.js';
import {isDisabled} from "bootstrap/js/src/util/index.js";

export default {
    name: "Create",
    data() {
        return {
            name: null,
            description: null,
            error_name: null,
            error_description: null,
        }
    },
    methods: {
        store() {
            api.post('/api/projects/', {name: this.name, description: this.description})
                .then(res => {
                    console.log(res);
                    if (res.status === 201) {
                        this.$router.push({name: 'project.index'})
                    }
                })
                .catch(error => {
                    console.log(error.message)
                    if(error.response.data.errors.message ){
                        console.log(1)
                    }
                    // if (error.response.data.errors.name) {
                    //     this.error_name = error.response.data.errors.name
                    // }
                    // if (error.response.data.errors.description) {
                    //     this.error_description = error.response.data.errors.description
                    // }
                })
        },
    },
    computed: {
        isDisabled() {
            return this.name && this.description
        }
    }
}
</script>

<template>
    <div class="customForm">
        <div class="row mb-3">
            <h3>Project</h3>
        </div>
        <div class="w-25 customBorder p-4">
            <div class="mb-3">
                <input v-model="name" type="text" class="form-control" placeholder="name">
                <div v-if="error_name" class="text-danger">{{ this.error_name }}</div>
            </div>
            <div class="mb-3">
                <input v-model="description" type="text" class="form-control mb-3" placeholder="description">
                <div v-if="error_description" class="text-danger">{{ this.error_description }}</div>
            </div>
            <i class="fa-regular fa-lock"></i>
            <input :disabled="!isDisabled" @click.prevent="store" type="submit" value="Add" class="btn btn-primary">
        </div>
    </div>
</template>

<style scoped>

</style>
