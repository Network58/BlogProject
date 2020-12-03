import Vue from 'vue'
import Router from 'vue-router'

Vue.use (Router)

import firstPage from './components/pages/myvuePage'
import basicVue from './components/pages/basicVue'

import home from './components/pages/home.vue'
import tags from './admin/pages/tags.vue'
import category from './admin/pages/category.vue'
import adminUsers from './admin/pages/adminUsers.vue'
import login from './admin/pages/login.vue'
import roles from './admin/pages/roles.vue'
import assignRole from './admin/pages/assignRole.vue'
import createBlog from './admin/pages/createBlog.vue'
import blogs from './admin/pages/blogs.vue'
import editblog from './admin/pages/editblog.vue'
import notfound from './admin/pages/notfound.vue'


const routes = [
    //projectroutes

   {
        path:'/',
        component: home,
        name: 'home' 
    },
    
    {
        path:'/tags',
        component: tags,
        name: 'tags'
    },
    
    {
        path:'/category',
        component: category,
        name: 'category'
    },
    {
        path:'/adminusers',
        component: adminUsers,
        name: 'adminUsers'
    },
    {
        path:'/login',
        component: login,
    },
    {
        path:'/roles',
        component: roles,
        name: 'roles'
    },
    {
        path:'/assignrole',
        component: assignRole,
        name: 'assignRole'
    },
    {
        path: '/createBlog',
        component: createBlog,
        name: 'createBlog'

    },
    {
        path: '/blogs',
        component: blogs,
        name: 'blogs'

    },
    {
        path: '/editblog/:id',
        component: editblog,
        name: 'editblog'

    },
    {
        path: '*',
        component: notfound,
        name: 'notfound'

    },
    



    
    {
        path: '/my-first-route',
        component: firstPage,
    },

    {
        path: '/basicvue',
        component: basicVue,
    }
]

 export default new Router ({
     mode:'history',
     routes
 })