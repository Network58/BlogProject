require('./bootstrap');

window.Vue = require('vue');
Vue.component('mainapp', require ('./components/mainapp.vue') .default);

import router from './router';
import ViewUI from 'view-design';
import store from './store';
import 'view-design/dist/styles/iview.css';

Vue.use(ViewUI);
import common from './common';
Vue.mixin(common);
import jsonToHtml from './jsonToHtml';
Vue.mixin(jsonToHtml);
// import Warning from '@editorjs/warning'
import CKEditor from 'ckeditor4-vue';
Vue.use( CKEditor );
import Editor from 'vue-editor-js'
Vue.use(Editor)

const app = new Vue({
    el: '#app',
    router,
    store
})