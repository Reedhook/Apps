<script>
import api from "../../../api.js";

export default {
    name: "EditReleaseType",
    data() {
        return {
            releases_types: null
        }
    },
    methods: {
        showData(id) {
            this.$parent.openForm(id);
        },
        getReleasesTypes() {
            api.get('/api/releases_types/')
                .then(res => {
                    this.releases_types = res.data.body.releases_types
                })
        },
        selectReleaseType(id) {
            this.$parent.selectedReleaseType = this.$parent.selectedReleaseType === id ? null : id
        }
    },
    mounted() {
        this.getReleasesTypes();
    }
}
</script>

<template>
    <div class="mb-3">
        Выберите тип релиза:
        <button id="add_ReleaseType" @click="this.showData('#addReleaseType')" class="btn"><i class="gg-add"></i>
        </button>
        <div v-if="this.$parent.selectedReleaseType">
            <p class="selectedModel">
                {{
                    this.releases_types.find(release_type => release_type.id === this.$parent.selectedReleaseType).name
                }}
            </p>
        </div>
        <div v-else>
            <p class="selectedModel">
                Тип релиза
            </p>
        </div>
        <div id="addReleaseType" class="showForm">
            <form role="form" action="/requestFine" autocomplete="off" method="POST">
                <ul>
                    <li id="project_list" v-for="release_type in this.releases_types" :key="release_type.id"
                        class="btn"
                        :class="{ 'selected':  this.$parent.selectedReleaseType === release_type.id  }"
                        @click="selectReleaseType(release_type.id)">
                        {{ release_type.name }}
                    </li>
                </ul>
                <button type="button" class="btn btn-danger" @click="this.$parent.openForm('#addReleaseType')">
                    Закрыть
                </button>
            </form>
        </div>
    </div>
</template>

<style scoped>

</style>
