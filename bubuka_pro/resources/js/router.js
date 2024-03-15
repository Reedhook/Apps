import {createRouter, createWebHistory} from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('./components/Home.vue')
    },
    {
        path: '/users/login',
        name: 'user.login',
        component: () => import('./components/User/Login.vue')
    },
    {
        path: '/users/registration',
        name: 'user.registration',
        component: () => import('./components/User/Registration.vue')
    },
    {
        path: '/users/forgot',
        name: 'user.forgot',
        component: () => import('./components/User/ForgotPassword.vue')
    },
    {
        path: '/users/reset/:token',
        name: 'user.reset',
        component: () => import('./components/User/ResetPassword.vue')
    },
    {
        path: '/projects/create',
        name: 'project.create',
        component: () => import('./components/Project/Create.vue')
    },
    {
        path: '/projects/index',
        name: 'project.index',
        component: () => import('./components/Project/Index.vue')
    },
    {
        path: '/projects/:id',
        name: 'project.show',
        component: () => import('./components/Project/Show.vue')
    },
    {
        path: '/projects/:id/edit',
        name: 'project.edit',
        component: () => import('./components/Project/Edit.vue')
    },
    {
        path: '/projects/:project_id/platforms/:platform_id',
        name: 'project.platform',
        component: () => import('./components/Project/Edit.vue')
    },
    {
        path: '/platforms/create',
        name: 'platform.create',
        component: () => import ('./components/Platform/Create.vue'),
    },
    {
        path: '/platforms/:id/edit',
        name: 'platform.edit',
        component: () => import ('./components/Platform/Edit.vue'),
    },
    {
        path: '/platforms/:id',
        name: 'platform.show',
        component: () => import ('./components/Platform/Show.vue'),
    },
    {
        path: '/platforms/:id/delete',
        name: 'platform.delete',
        component: () => import ('./components/Platform/Show.vue'),
    },
    {
        path: '/platforms',
        name: 'platform.index',
        component: () => import ('./components/Platform/Index.vue'),
    },
    {
        path: '/releases/index',
        name: 'release.index',
        component: () => import('./components/Release/Index.vue')
    },
    {
        path: '/releases/create',
        name: 'release.create',
        component: () => import ('./components/Release/Create/Create.vue'),
    },
    {
        path: '/releases/:id/edit',
        name: 'release.edit',
        component: () => import ('./components/Release/Edit/Edit.vue'),
    },
    {
        path: '/releases/:id',
        name: 'release.show',
        component: () => import ('./components/Release/Show.vue'),
    },
    {
        path: '/releases/',
        name: 'release.preview',
        props: (route) => ({query: route.query}),
        component: () => import ('./components/Release/Preview/Preview.vue'),
    },
    {
        path: '/releases/:id/delete',
        name: 'release.delete',
        component: () => import ('./components/Release/Show.vue'),
    },
    {
        path: '/techs/create',
        name: 'tech.create',
        component: () => import('./components/Tech/Create.vue')
    },
    {
        path: '/techs/index',
        name: 'tech.index',
        component: () => import('./components/Tech/Index.vue')
    },
    {
        path: '/techs/:id',
        name: 'tech.show',
        component: () => import('./components/Tech/Show.vue')
    },
    {
        path: '/techs/:id/edit',
        name: 'tech.edit',
        component: () => import('./components/Tech/Edit.vue')
    },
    {
        path: '/changelogs/index',
        name: 'changelog.index',
        component: () => import('./components/Changelog/Index.vue')
    },
    {
        path: '/changelogs/create',
        name: 'changelog.create',
        component: () => import('./components/Changelog/Create.vue')
    },
    {
        path: '/changelogs/:id/edit',
        name: 'changelog.edit',
        component: () => import('./components/Changelog/Edit.vue')
    },
    {
        path: '/releases_types/index',
        name: 'release_type.index',
        component: () => import('./components/ReleaseType/Index.vue')
    },
    {
        path: '/releases_types/create',
        name: 'release_type.create',
        component: () => import('./components/ReleaseType/Create.vue')
    },
    {
        path: '/releases_types/:id/edit',
        name: 'release_type.edit',
        component: () => import('./components/ReleaseType/Edit.vue')
    },
    {
        path: '/releases_types/:id',
        name: 'release_type.show',
        component: () => import ('./components/ReleaseType/Show.vue'),
    },
    {
        path: '/releases_types/:id/delete',
        name: 'release_type.delete',
        component: () => import ('./components/ReleaseType/Show.vue'),
    },
    {
        path: '/:pathMatch(.*)*',
        name: '404',
        component: () => import('./components/Errors.vue')
    }

];
const router = createRouter({
    history: createWebHistory(),
    routes
});
router.beforeEach((to, Route, next) => {
    const accessToken = localStorage.getItem('access_token')

    if (!accessToken) {
        if (to.name === 'user.login' || to.name === 'user.registration' || to.name === 'user.forgot' || to.name === 'user.reset') {

            return next()
        } else {

            return next({
                name: 'user.login'
            })
        }
    }
    if (to.name === 'user.login' && accessToken) {
        return next({
            name: 'platform.index'
        })
    }
    next();
})
export default router;
