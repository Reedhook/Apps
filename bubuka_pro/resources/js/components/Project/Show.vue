<script>
import api from "../../api.js";

export default {
    name: "Show",
    data() {
        return {
            project: null,
            platforms: null,
            users: null,
            user: null,
            formOpen: false,
            selectedPlatforms: [], // Массив для хранения выбранных платформ
            selectedUsers: [] // Массив для хранения выбранных пользователей
        }
    },
    methods: {
        deleteDate(id) {
            api.delete(`/api/projects/${id}`)
                .then(res => {
                    this.$router.push({name: 'project.index'});
                })
        },
        getProject() {
            api.get(`/api/projects/${this.$route.params.id}`)
                .then(res => {
                    this.project = res.data.body.project
                })
        },
        getPlatforms() {
            api.get(`/api/platforms/`)
                .then(res => {
                    this.platforms = res.data.body.platforms
                })
        },
        getUsers() {
            api.get(`/api/auth/users/`)
                .then(res => {
                    this.users = res.data.body.users
                })
        },
        openForm(id) {
            this.getPlatforms();
            this.getUsers();
            const form = document.querySelector(id);

            if (form.classList.contains('open')) {
                // Если форма уже открыта, закрываем ее
                form.classList.remove('open');
                this.formOpen = false;
            } else {
                // Закрываем все другие формы
                document.querySelectorAll('.open').forEach((otherForm) => {
                    otherForm.classList.remove('open');
                });

                // Открываем форму с переданным id
                form.classList.add('open');
                this.formOpen = true;
            }
        },
        selectObj(Id, model) {
            let selectedModels;
            if (model === 'user') {
                selectedModels = 'selectedUsers'
            }
            if (model === 'platform') {
                selectedModels = 'selectedPlatforms'
            }
            // Проверяем, есть ли id в массиве выбранных платформ
            const index = this[selectedModels].indexOf(Id);
            if (index !== -1) {
                // Если id уже есть в массиве, удаляем его
                this[selectedModels].splice(index, 1);
            } else {
                // Если id отсутствует в массиве, добавляем его
                this[selectedModels].push(Id);
            }
        },
        sendSelectedData(model) {
            console.log(model);
            let uri, selectedModels;
            if (model === 'user') {
                uri = 'users'
                selectedModels = 'selectedUsers'
            }
            if (model === 'platform') {
                uri = 'platforms'
                selectedModels = 'selectedPlatforms'
            }
            this[selectedModels].forEach(Id => {
                api.patch(`/api/projects/${this.$route.params.id}/${uri}/${Id}`)
                    .then(res => {
                        this[selectedModels] = [];
                        this.formOpen = false; // Закрываем форму после отправки
                        this.getProject(); // Обновляем данные о проекте
                    })
                    .catch(error => {
                        console.error(`Ошибка при отправке данных с id ${Id}: ${error.message}`);
                    });
            });
        },
        deleteSelectedData(model) {
            let uri;
            if (model === 'user') {
                uri = 'users'
            }
            if (model === 'platform') {
                uri = 'platform'
            }
            this.selectedPlatforms.forEach(Id => {
                api.delete(`/api/projects/${this.$route.params.id}/${uri}/${Id}`)
                    .then(res => {
                        this.selectedPlatforms = [];
                        // Проверяем количество оставшихся платформ
                        this.getProject(); // Обновляем данные о проекте
                    })
                    .catch(error => {
                        console.error(`Ошибка при удалении платформы с id ${Id}: ${error.message}`);
                    });
            });
        },
        redirectToPlatform() {
            // Перенаправление на страницу выбранной платформы
            this.selectedPlatforms.forEach(platformId => {
                this.$router.push({name: 'platform.show', params: {id: platformId}});
            });
        }
    },
    mounted() {
        this.getProject();
    },
    computed: {
        filteredPlatforms() {
            if (this.project && this.platforms) {
                return this.platforms.filter(platform => {
                    return !this.project.platforms.some(p => p.id === platform.id);
                });
            }
            return [];
        },
        filteredUsers() {
            if (this.project && this.users) {
                return this.users.filter(user => {
                    return !this.project.users.some(p => p.id === user.id);
                });
            }
            return [];
        }
    },
}
</script>

<template>
    <div class="w-50" v-if="project">
        <table class="table">
            <thead>
            <tr>
                <th scope="col">Id</th>
                <th scope="col">Name</th>
                <th scope="col">Description</th>
                <th scope="col">Platforms</th>
                <th scope="col">Users</th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
                <th scope="col"></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>{{ project.id }}</td>
                <td> {{ project.name }}</td>
                <td> {{ project.description }}</td>
                <td>
                    <select v-model="selectedPlatforms" multiple class="select-list" @change="redirectToPlatform">
                        <option v-for="platform in project.platforms" :key="platform.id" :value="platform.id">
                            {{ platform.name }}
                        </option>
                    </select>
                </td>
                <td>
                    <select v-model="selectedUsers" multiple class="select-list">
                        <option v-for="user in project.users" :key="user.id" :value="user.id">
                            {{ user.email }}
                        </option>
                    </select>
                </td>
                <td>
                    <template v-if="formOpen">
                        <div class="btn" id="edit">
                            <i class="gg-pen"></i>
                        </div>
                    </template>
                    <template v-else>
                        <router-link :to="{name:'project.edit', params:{id:project.id}}" id="edit" class="btn"><i
                            class="gg-pen"></i></router-link>
                    </template>
                </td>
                <td>
                    <template v-if="formOpen">
                        <div class="btn">
                            <i class="gg-trash"></i>
                        </div>
                    </template>
                    <template v-else>
                        <a @click.prevent="deleteDate(project.id)" href="#" id="trash" class="btn"><i
                            class="gg-trash"></i></a>
                    </template>
                </td>
                <td>
                    <button id="add_platform" @click="openForm('#addPlatform')" class="btn"><i class="gg-add"></i>
                    </button>
                    <div id="addPlatform" v-if="project" class="showForm">
                        <form role="form" action="/requestFine" autocomplete="off" method="POST">
                            <ul>
                                <li id="project_list" v-for="platform in filteredPlatforms" :key="platform.id"
                                    class="btn"
                                    :class="{ 'selected': selectedPlatforms.includes(platform.id) }"
                                    @click="selectObj(platform.id, 'platform')">
                                    {{ platform.name }}
                                </li>
                            </ul>
                            <button type="button" class="btn btn-success" @click="sendSelectedData('platform')">Добавить
                                платформу
                            </button>
                        </form>
                    </div>
                </td>
                <td>
                    <button id="delete_platform" @click="openForm('#deletePlatform')" class="btn"><i
                        class="gg-math-minus"></i></button>
                    <div id="deletePlatform" class="showForm">
                        <form role="form" action="/requestFine" autocomplete="off" method="POST">
                            <ul>
                                <li id="project_list" v-for="platform in project.platforms" :key="platform.id"
                                    class="btn"
                                    :class="{ 'selected': selectedPlatforms.includes(platform.id) }"
                                    @click="selectObj(platform.id, 'platform')">
                                    {{ platform.name }}
                                </li>
                            </ul>
                            <button type="button" class="btn btn-danger" @click="deleteSelectedData('platform')">
                                Удалить платформу
                            </button>
                        </form>
                    </div>
                </td>
                <td>
                    <button id="add_user" @click="openForm('#addUser')" class="btn">
                        <i class="gg-user-add"></i>
                    </button>
                    <div id="addUser" v-if="project" class="showForm">
                        <form role="form" action="/requestFine" autocomplete="off" method="POST">
                            <ul>
                                <li id="project_list" v-for="user in filteredUsers" :key="user.id"
                                    class="btn"
                                    :class="{ 'selected': selectedUsers.includes(user.id) }"
                                    @click="selectObj(user.id, 'user')">
                                    {{ user.email }}
                                </li>
                            </ul>
                            <button type="button" class="btn btn-success" @click="sendSelectedData('user')">
                                Добавить пользователя
                            </button>
                        </form>
                    </div>
                </td>
                <td>
                    <button id="delete_user" @click="openForm('#deleteUser')" class="btn"><i class="gg-user-remove"></i>
                    </button>
                    <div id="deleteUser" class="showForm">
                        <form role="form" action="/requestFine" autocomplete="off" method="POST">
                            <ul>
                                <li id="project_list" v-for="user in project.users" :key="user.id"
                                    class="btn"
                                    :class="{ 'selected': selectedUsers.includes(user.id) }"
                                    @click="selectObj(user.id, 'user')">
                                    {{ user.email }}
                                </li>
                            </ul>
                            <button type="button" class="btn btn-danger" @click="deleteSelectedData('data')">
                                Удалить пользователя
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>

</template>

<style scoped>

#project_list {
    display: block;
    padding: 10px 0;
    margin: 0;
}

.selected {
    background-color: #3498db; /* Цвет подсветки для выбранных строк */
    color: #fff; /* Цвет текста для выбранных строк */
}

select {
    border: none; /* Убираем границу у выпадающего списка */
    background-color: transparent; /* Устанавливаем прозрачный фон */
    outline: none; /* Убираем обводку при фокусе */
}

.select-list {
    width: 100%; /* Ширина списка равна 100% */
    padding: 10px; /* Внутренний отступ */
    margin: 0; /* Убираем внешние отступы */
    text-align: center; /* Выравниваем текст по центру */
}

.table {
    margin: 0 auto; /* Центрируем таблицу по горизонтали */
}
</style>
