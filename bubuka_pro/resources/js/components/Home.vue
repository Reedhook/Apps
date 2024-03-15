<script>
export default {
    name: "Home"
}
</script>

<template>
    <div class="container">
        <div class="row">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col"></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <h3>Changelogs</h3>
                        <div class="Entity">
                            <router-link :to="{name:'changelog.index'}" id="watch_list" class="m-lg-2"><i
                                class="gg-list"></i></router-link>
                            <router-link :to="{name:'changelog.create'}" id="add_date"><i
                                class="m-lg-2 gg-add-r"></i>
                            </router-link>
                        </div>
                    </td>
                    <td>
                        <h3>ReleaseType</h3>
                        <div class="Entity">
                            <router-link :to="{name:'release_type.index'}" id="watch_list" class="m-lg-2"><i
                                class="gg-list"></i></router-link>
                            <router-link :to="{name:'release_type.create'}" id="add_date"><i
                                class="m-lg-2 gg-add-r"></i>
                            </router-link>
                        </div>
                    </td>
                    <td>
                        <h3>Projects</h3>
                        <div class="Entity">
                            <i class="m-lg-2 gg-lock" id="admin"></i>
                            <router-link :to="{name:'project.index'}" class="m-lg-2" id="watch_list"><i
                                class="gg-list"></i>
                            </router-link>
                            <router-link :to="{name:'project.create'}" id="add_date"><i class="m-lg-2 gg-add-r"></i>
                            </router-link>
                        </div>
                    </td>
                    <td>
                        <h3>Platforms</h3>
                        <div class="Entity">
                            <router-link :to="{name:'platform.index'}" class="m-lg-2" id="watch_list"><i
                                class="gg-list"></i>
                            </router-link>
                            <router-link :to="{name:'platform.create'}" id="add_date"><i
                                class="m-lg-2 gg-add-r"></i>
                            </router-link>
                        </div>
                    </td>
                    <td>
                        <h3>Releases</h3>
                        <div class="Entity">
                            <router-link :to="{name:'release.index'}" class="m-lg-2" id="watch_list"><i
                                class="gg-list"></i>
                            </router-link>
                            <router-link :to="{name:'release.create'}" id="add_date"><i class="m-lg-2 gg-add-r"></i>
                            </router-link>
                        </div>
                    </td>
                    <td>
                        <h3>Technical requirements</h3>
                        <div class="Entity">
                            <router-link :to="{name:'tech.index'}" class="m-lg-2" id="watch_list"><i
                                class="gg-list"></i>
                            </router-link>
                            <router-link :to="{name:'tech.create'}" id="add_date"><i class="m-lg-2 gg-add-r"></i>
                            </router-link>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>

.Entity {
    display: flex;
    align-items: center;
    margin-left: auto;
}

table {
    justify-content: center; /* Выравнивание элементов по горизонтали */
    align-items: center; /* Выравнивание элементов по вертикали */
}
td{
    display: flex; align-items: center;
}
.container {
    display: flex;
    justify-content: center;
}

.row {
    width: 50%;
}
</style>
