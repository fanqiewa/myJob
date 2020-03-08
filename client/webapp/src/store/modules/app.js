
import Cookies from 'js-cookie';
import { 
  getMenuByRouter,
  getBreadCrumbList,
  getHomeRoute,
  localRead,
  localSave,
  setTagNavListInLocalstorage,
  getTagNavListFromLocalstorage,
  getNextRoute,
  getRouteTitleHandled,
  routeEqual,
  routeHasExist
} from '../../libs/utils';
import config from '@/config'
import router from '@/router'
import routers from '@/router/routers'
import { saveErrorLogger } from '@/api/data'
const { homeName } = config

const closePage = (state, route) => {
  const nextRoute = getNextRoute(state.tagNavList, route)
  state.tagNavList = state.tagNavList.filter(item => {
    return !routeEqual(item, route)
  })
  router.push(nextRoute)
}

const app = {
  state: {
    breadCrumbList: [],//面包屑
    menuList: [], //左侧菜单
    homeRoute: {},
    hasReadErrorPage: false,
    errorList: [],
    local: localRead('local'),
    tagNavList: []//tag数组
  },
  getters: {
    // menuList: (state, getters, rootState) => getMenuByRouter(routers, rootState.user.access),
    menuList: (state, getters, rootState) => getMenuByRouter(routers, Cookies.get('access')),
    errorCount: state => state.errorList.length
  },
  mutations: {
    setBreadCrumb (state, route) { //设置面包屑
      state.breadCrumbList = getBreadCrumbList(route, state.homeRoute)
    },
    setHomeRoute (state, routes) {//设置home路由
      state.homeRoute = getHomeRoute(routes, homeName)
    },
    //设置语言
    setLocal (state, lang) {
      localSave('local', lang)
      state.local = lang
    },

    //设置导航标签列表
    setTagNavList (state, list) {
      let tagList = []
      if (list) {
        tagList = [...list]
      } else tagList = getTagNavListFromLocalstorage() || []
      if (tagList[0] && tagList[0].name !== homeName) tagList.shift()
      let homeTagIndex = tagList.findIndex(item => item.name === homeName)
      if (homeTagIndex > 0) {
        let homeTag = tagList.splice(homeTagIndex, 1)[0]
        tagList.unshift(homeTag)
      }
      state.tagNavList = tagList
      setTagNavListInLocalstorage([...tagList])
    },
    closeTag (state, route) {
      let tag = state.tagNavList.filter(item => routeEqual(item, route))
      route = tag[0] ? tag[0] : null
      if (!route) return
      closePage(state, route)
    },
    addTag (state, { route, type = 'unshift' }) {
      let router = getRouteTitleHandled(route)
      if (!routeHasExist(state.tagNavList, router)) {
        if (type === 'push') state.tagNavList.push(router)
        else {
          if (router.name === homeName) state.tagNavList.unshift(router)
          else state.tagNavList.splice(1, 0, router)
        }
        setTagNavListInLocalstorage([...state.tagNavList])
      }
    },

    //设置错误信息
    setHasReadErrorLoggerStatus (state, status = true) {
      state.hasReadErrorPage = status
    },
    addError (state, error) {
      state.errorList.push(error)
      console.log(state.errorList)
    },
  },
  actions: {
    addErrorLog ({ commit, rootState }, info) {
      if (!window.location.href.includes('error_logger_page')) commit('setHasReadErrorLoggerStatus', false)
      const { user: { token, userId, userName } } = rootState
      let data = {
        ...info,
        time: Date.parse(new Date()),
        token,
        userId,
        userName
      }
      // saveErrorLogger(info).then(() => {
        commit('addError', data)
      // })
    }
  }
}

export default app;


