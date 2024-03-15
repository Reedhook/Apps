<script>
import {isDisabled} from "bootstrap/js/src/util/index.js";
import api from '../../api.js';

export default {
    name: "Create",
    data() {
        return {
            news: null,
            changes: null,
            error_news: null,
            error_changes: null,

        }
    },
    methods: {
        isDisabled,
        store() {
            api.post('/api/changes/', {news: this.news, changes: this.changes})
                .then(res => {
                    this.$router.push({name: 'changelog.index'})
                })
        }
    },
    computed: {
        isDisabled() {
            return this.news && this.changes
        }
    }
}
</script>

<template>
    <div class="d-flex flex-column justify-content-center align-items-center" style="height: 100vh;">
        <div class="row mb-3">
            <h3>Changelog</h3>
        </div>
        <div class="w-25 border border rounded p-4">
            <div class="mb-3">
                <input v-model="news" type="text"  placeholder="news" class="form-control">
                <div v-if="error_news" class="text-danger">{{ this.error_news }}</div>
            </div>
            <div class="mb-3">
                <input v-model="changes" type="text" class="form-control mb-3" placeholder="changes">
                <div v-if="error_changes" class="text-danger">{{ this.error_changes }}</div>
            </div>
            <input :disabled="!isDisabled" @click.prevent="store" type="submit" value="Add" class="btn btn-primary">
        </div>
    </div>
</template>

<style scoped>

</style>
