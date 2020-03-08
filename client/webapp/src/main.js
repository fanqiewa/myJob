// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import iView from 'iview';
import 'iview/dist/styles/iview.css';
import store from './store'
import axios from './libs/api.request';
import config from '@/config'
import i18n from '@/locale'
import Base64 from 'js-base64'
Vue.use(iView, {
  i18n: (key, value) => i18n.t(key, value)
})
Vue.prototype.$axios = axios.request()
Vue.prototype.Base64 = Base64.Base64
Vue.config.productionTip = false

/**
 * @description 全局注册应用配置
 */
Vue.prototype.$config = config


/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  i18n,
  render: h => h(App)
})