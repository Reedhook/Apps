<script>
import {isDisabled} from "bootstrap/js/src/util/index.js";
import api from '../../api.js';

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
        isDisabled,
        store() {
            api.post('/api/releases_types/', {name: this.name, description: this.description})
                .then(res => {
                    this.$router.push({name: 'release_type.index'})
                })
        }
    },
    computed: {
        isDisabled() {
            return this.name && this.description
        }
    }
}
</script>

<template>
    <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <div class="row mb-3">
            <h3>Release_type</h3>
        </div>
        <div class="w-25 border border rounded p-4">
            <div class="mb-3">
                <input v-model="name" type="text"  placeholder="name" class="form-control">
                <div v-if="error_name" class="text-danger">{{ this.error_name }}</div>
            </div>
            <div class="mb-3">
                <input v-model="description" type="text" class="form-control mb-3" placeholder="description">
                <div v-if="error_description" class="text-danger">{{ this.error_description }}</div>
            </div>
            <input :disabled="!isDisabled" @click.prevent="store" type="submit" value="Add" class="btn btn-primary">
        </div>
    </div>
</template>

<style scoped>

</style>
