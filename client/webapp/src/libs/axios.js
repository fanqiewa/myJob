import axios from 'axios'
import store from '@/store'
import Cookies from 'js-cookie'
import Vue from 'vue'
// import { Spin } from 'iview'

const addErrorLog = errorInfo => {
    const { statusText, status, request: { responseURL } } = errorInfo
    let info = {
        type: 'ajax',
        code: status,
        mes: statusText,
        url: responseURL
    }
    if (!responseURL.includes('save_error_logger')) store.dispatch('addErrorLog', info)
}


class HttpRequest {
    constructor(baseUrl = baseUrl) {
        this.baseUrl = baseUrl
        this.queue = {}
    }
    getInsideConfig() {
        const config = {
            baseUrl: this.baseUrl,
            headers: {

            }
        }
        return config
    }
    destroy(url) {
        delete this.queue[url]
        if (!Object.keys(this.queue).length) {
            // Spin.hide()
        }
    }
    /**
     * 请求拦截器
     * @param {*} instance axios实例
     * @param {*} url 
     */
    interceptors(instance, url) {
        // 添加请求拦截器
        instance.interceptors.request.use(config => {
            // 添加全局的loading...
            if (Object.keys(this.queue).length) {
                // Spin.show() // 不建议开启，因为界面不友好
            }
            this.queue[url] = true
            let token = Cookies.get('token');
            if (token) {
                config.headers['X-Token'] = token // 让每个请求携带自定义token 请根据实际情况自行修改
            }
            return config
        }, error => {
            return Promise.reject(error)
        })
        // 响应拦截
        instance.interceptors.response.use(res => {
            this.destroy(url)
            const { data, status } = res
            if (data.code < 1000) { //200 500 成功
                return { data, status }
            } else {
                if (data.code === 50008) {
                    Vue.prototype.$Modal.confirm({
                        title: '系统提示',
                        content: '<p>' + data.msg + '</p>',
                        onOk: () => {
                            //退出登录
                            Cookies.remove('user');
                            Cookies.remove('token');
                            Cookies.remove('access');
                            localStorage.clear();
                            location.reload()// 为了重新实例化vue-router对象 避免bug
                        },
                        onCancel: () => {
                            // 退出登录
                        }
                    })
                } else if (data.code === 50009) {
                    Vue.prototype.$Modal.confirm({
                        title: '系统提示',
                        content: '<p>' + data.msg + '</p>'
                    });
                }
                return Promise.reject('error')
            }
        }, error => {
            this.destroy(url)
            let errorInfo = error.response
            if (!errorInfo) {
                const { request: { statusText, status }, config } = JSON.parse(JSON.stringify(error))
                errorInfor = {
                    statusText,
                    status,
                    request: { responseUrl: config.url }
                }
            }
            addErrorLog(errorInfo)
            return Promise.reject(error)
        })
    }
    request(options) {
        options = Object.assign(this.getInsideConfig(), options)
        const instance = axios.create(options)
        this.interceptors(instance, options.url)
        return instance
    }
}

export default HttpRequest