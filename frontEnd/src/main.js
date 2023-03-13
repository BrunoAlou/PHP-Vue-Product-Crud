import Vue from 'vue'
import App from './App.vue'
import axios from 'axios'
import router from './router'
import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(BootstrapVue)

axios.defaults.baseURL = 'http://localhost:8080/';

Vue.prototype.$apiUrl = 'http://localhost:8080';

new Vue({
  router,
  render: h => h(App),
}).$mount('#app')
