<script>
import api from '../../api.js';

export default {
    name: "Index",
    data() {
        return {
            changelogs: null
        }
    },
    methods: {
        deleteChangelogs(id, models) {
            api.delete(`/api/${models}}/${id}`)
                .then(res => {
                    this.getChangelogs()
                })
        },
        getChangelogs() {
            api.get('/api/changes')
                .then(res => {
                    this.changelogs = res.data.body.changelogs
                })
        }

    },
    mounted() {
        this.getChangelogs()
    }
}
</script>

<template>
    <div class="w-25">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Changes</th>
                <th scope="col">News</th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(changelog, i) in changelogs">
                <td>{{ i + 1 }}</td>
                <td>
                    {{changelog.changes}}
                </td>
                <td>
                   {{changelog.news}}
                </td>
                <td>
                    <router-link :to="{name:'changelog.edit', params:{id:changelog.id}}" id="edit" class="btn"><i
                        class="gg-pen"></i></router-link>
                </td>
                <td>
                    <a @click.prevent="deleteChangelogs(changelog.id)" href="#" id="trash" class="btn"><i
                        class="gg-trash"></i></a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>
