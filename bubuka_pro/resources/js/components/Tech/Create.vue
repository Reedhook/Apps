<script>
import api from '../../api.js';
import {isDisabled} from "bootstrap/js/src/util/index.js";

export default {
    name: "Create",
    data() {
        return {
            os_type: null,
            specifications: null,
            error_os_type: null,
            error_specifications: null,
        }
    },
    methods: {
        isDisabled,
        store() {
            api.post('/api/techs_reqs/', {os_type: this.os_type, specifications: this.specifications})
                .then(res => {
                    this.$router.push({name: 'tech.index'})
                })
                .catch(error=>{
                    if(error.response.data.errors.os_type){
                        this.error_os_type = error.response.data.errors.os_type
                    }
                    if(error.response.data.errors.specifications){
                        this.error_specifications = error.response.data.errors.specifications
                    }
                })
        }
    },
    computed: {
        isDisabled() {
            return this.os_type || this.specifications
        }
    }
}
</script>

<template>
    <div class="customForm">
        <div class="row mb-3">
            <h3>Technical requirement</h3>
        </div>
        <div class="w-25 customBorder p-4">
            <div class="mb-3">
                <input v-model="os_type" type="text" class="form-control" placeholder="os_type">
                <div v-if="error_os_type" class="text-danger">{{ this.error_os_type }}</div>
            </div>
            <div class="mb-3">
                <input v-model="specifications" type="text" class="form-control mb-3" placeholder="specifications">
                <div v-if="error_specifications" class="text-danger">{{ this.error_specifications }}</div>
            </div>
            <i class="fa-regular fa-lock"></i>
            <input :disabled="!isDisabled" @click.prevent="store" type="submit" value="Add" class="btn btn-primary">
        </div>
    </div>
</template>

<style scoped>

</style>
