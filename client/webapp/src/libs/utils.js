import Cookies from 'js-cookie';
import { forEach, hasOneOf, objEqual } from '@/libs/tools'
import config from '@/config'

const { title, useI18n, cookieExpires } = config;

export const TOKEN_KEY = 'token'

export const setToken = (token) => {
    Cookies.set(TOKEN_KEY, token, { expires: cookieExpires || 1 })
    console.log(Cookies.get('token'))
}

export const getToken = () => {
    const token = Cookies.get(TOKEN_KEY)
    if (token) return token
    else return false
}

/**
 * 获取左侧菜单
 * @param {Array} list 路由数组
 * @param {Array} access 权限
 */
export const getMenuByRouter = (list, access) => {
    let res = [];
    forEach(list, item => {
        if (!item.meta || (item.meta && !item.meta.hideInMenu)) {
            let obj = {
                icon: (item.meta && item.meta.icon) || '',
                name: item.name,
                meta: item.meta
            }
            if ((hasChild(item) || (item.meta && item.meta.showAlways)) && showThisMenuEle(item, access)) {
                //递归
                obj.children = getMenuByRouter(item.children, access);
            }
            if (item.meta && item.meta.href) obj.href = item.meta.href;
            if (showThisMenuEle(item, access)) res.push(obj);
        }
    })
    return res;
}

/**
 * 判断路由是否有子路由
 * @param {Array} item 
 */
export const hasChild = (item) => {
    return item.children && item.children.length !== 0
}

/**
 * 通过路由权限判断是否显示
 * @param {item为一个数组} item 
 * @param {access为权限} access 
 */
const showThisMenuEle = (item, access) => {
    //有权限并且权限存在，返回true；没有权限直接返回true
    if (item.meta && item.meta.access && item.meta.access.length) {
        if (hasOneOf(item.meta.access, access)) return true;
        else return false;
    } else return true;
}

/**
 * 显示标题
 * @param {Array} item 路由 
 * @param {*} vm 组件
 */
export const showTitle = (item, vm) => {
    let { title, __titleIsFunction__ } = item.meta;
    if (!title) return;
    if (useI18n) {
        /**
         * title = "{{状态管 理 }}" ==》 状态管理
         */
        if (title.includes('{{') && title.includes('}}') && useI18n) {
            title = title.replace(/({{[\s\S]+?}})/, function (m, str) {
                //第一个参数：正则所匹配到的字符
                //第二个参数：捕获子表达式所匹配到的内容（也就是括号包裹的）
                //...: 捕获子表达式所匹配到的内容（也就是括号包裹的）
                //倒数第二个个参数：正则匹配到的每段字段的第一个字符的索引
                //倒数第一个个参数：用于匹配的字符串主体

                /**
                 * str => {{abcde}}
                 */
                return str.replace(/{{([\s\S]*)}}/, function (m, _) {
                    /**
                     * _ => abcde
                     */
                    return vm.$t(_.trim())  //$t在vue的原型上
                })
            })
        } else if (__titleIsFunction__) {
            title = item.meta.title
        } else {
            title = vm.$t(item.name);
        }
    } else title = (item.meta && item.meta.title) || item.name

    return title;

}

/**
 * @param {Array} routeMetched 当前路由metched(路由活动路径)
 * @returns {Array}
 */
export const getBreadCrumbList = (route, homeRoute) => {
    // console.log(route)
    // console.log(homeRoute)
    let homeItem = { ...homeRoute, icon: homeRoute.meta.icon }
    let routeMetched = route.matched
    if (routeMetched.some(item => item.name === homeRoute.name)) return [homeItem] //有'home'直接返回
    let res = routeMetched.filter(item => {
        return item.meta === undefined || !item.meta.hideInBread
      }).map(item => {
        let meta = { ...item.meta }
        if (meta.title && typeof meta.title === 'function') {
          meta.__titleIsFunction__ = true
          meta.title = meta.title(route)
        }
        let obj = {
          icon: (item.meta && item.meta.icon) || '',
          name: item.name,
          meta: meta
        }
        return obj
      })
      res = res.filter(item => {
        return !item.meta.hideInMenu
      })
      return [{ ...homeItem, to: homeRoute.path }, ...res] //to: homeRoute.path首页可以点击跳转
}

/**
 * @param {Array} routers 路由列表数组
 * @description 用于找到路由列表中name为home的对象
 * 如果有子路由，那么子路由的name必须为"home"，不然返回空对象
 */
 export const getHomeRoute = (routers, homeName = 'home') => {
    let i = -1
    let len = routers.length
    let homeRoute = {}
    while (++i < len) {
      let item = routers[i]
      if (item.children && item.children.length) {
        let res = getHomeRoute(item.children, homeName)
        if (res.name) return res
      } else {
        if (item.name === homeName) homeRoute = item
      }
    }
    return homeRoute;
 }

/**
 * 获取语言
 * @param {*} key 
 */
 export const localRead = (key) => {
    return localStorage.getItem(key) || ''
  }
//设置语言
  export const localSave = (key, value) => {
    localStorage.setItem(key, value)
  }

  
  /**
 * @description 本地存储和获取标签导航列表
 */
export const setTagNavListInLocalstorage = list => {
  localStorage.tagNaveList = JSON.stringify(list)
}
/**
 * @returns {Array} 其中的每个元素只包含路由原信息中的name, path, meta三项
 */
export const getTagNavListFromLocalstorage = () => {
  const list = localStorage.tagNaveList
  return list ? JSON.parse(list) : []
}


/**
 * @param {*} list 现有标签导航列表
 * @param {*} newRoute 新添加的路由原信息对象
 * @description 如果该newRoute已经存在则不再添加
 */
export const getNewTagList = (list, newRoute) => {
  const { name, path, meta } = newRoute
  let newList = [...list]
  if (newList.findIndex(item => item.name === name) >= 0) return newList
  else newList.push({ name, path, meta })
  return newList
}

/**
 * @description 根据name/params/query判断两个路由对象是否相等
 * @param {*} route1 路由对象
 * @param {*} route2 路由对象
 */
export const routeEqual = (route1, route2) => {
  const params1 = route1.params || {}
  const params2 = route2.params || {}
  const query1 = route1.query || {}
  const query2 = route2.query || {}
  return (route1.name === route2.name) && objEqual(params1, params2) && objEqual(query1, query2)
}

/**
 * @param {Array} list 标签列表
 * @param {String} name 当前关闭的标签的name
 */
export const getNextRoute = (list, route) => {
  let res = {} //下一个要显示的路由
  if (list.length === 2) {
    res = getHomeRoute(list)
  } else {
    const index = list.findIndex(item => routeEqual(item, route))
    if (index === list.length - 1) res = list[list.length - 2]
    else res = list[index + 1]
  }
  return res
}

export const getRouteTitleHandled = (route) => {
  let router = { ...route }
  let meta = { ...route.meta }
  let title = ''
  if (meta.title) {
    if (typeof meta.title === 'function') {
      meta.__titleIsFunction__ = true
      title = meta.title(router)
    } else title = meta.title
  }
  meta.title = title
  router.meta = meta
  return router
}




/**
 * @description 根据当前跳转的路由设置显示在浏览器标签的title
 * @param {Object} routeItem 路由对象
 * @param {Object} vm Vue实例
 */
export const setTitle = (routeItem, vm) => {
  const handledRoute = getRouteTitleHandled(routeItem)
  const pageTitle = showTitle(handledRoute, vm)
  const resTitle = pageTitle ? `${title} - ${pageTitle}` : title
  window.document.title = resTitle
}


/**
 * @param {*} access 用户权限数组，如 ['super_admin', 'admin']
 * @param {*} route 路由列表
 */
const hasAccess = (access, route) => {
  if (route.meta && route.meta.access) return hasOneOf(access, route.meta.access)
  else return true
}

/**
 * 权鉴
 * @param {*} name 即将跳转的路由name
 * @param {*} access 用户权限数组
 * @param {*} routes 路由列表
 * @description 用户是否可跳转到该页
 */
export const canTurnTo = (name, access, routes) => {
  const routePermissionJudge = (list) => {
    return list.some(item => {
      if (item.children && item.children.length) {
        return routePermissionJudge(item.children)
      } else if (item.name === name) {
        return hasAccess(access, item)
      }
    })
  }
  return routePermissionJudge(routes)
}

/**
 * @param {Number} times 回调函数需要执行的次数
 * @param {Function} callback 回调函数
 */
export const doCustomTimes = (times, callback) => {
  let i = -1
  while (++i < times) {
    callback(i)
  }
}
/**
 * 判断打开的标签列表里是否已存在这个新添加的路由对象
 */
export const routeHasExist = (tagNavList, routeItem) => {
  let len = tagNavList.length
  let res = false
  doCustomTimes(len, (index) => {
    if (routeEqual(tagNavList[index], routeItem)) res = true
  })
  return res
}