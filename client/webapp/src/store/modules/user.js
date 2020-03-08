
import {
  login,
  logout,
  getUserInfo
} from '@/api/user'
import { setToken, getToken } from '@/libs/utils'

const user = {
  state: {
    access: '',
    token: getToken(),
    userId: '',
    userName: '',
    hasGetInfo: false,
    avatarImgPath: ''
  },
  mutations: {
    setAvatar (state, avatarPath) {
      // state.avatarImgPath = avatarPath
      localStorage.avatorImgPath = avatarPath;
    },
    setUserId (state, id) {
      state.userId = id
    },
    setUserName (state, name) {
      state.userName = name
    },
    setToken (state, token) {
      state.token = token
      setToken(token)
    },
    setAccess (state, access) {
      state.access = access
    },
    setHasGetInfo (state, status) {
      // state.hasGetInfo = status
      localStorage.hasGetInfo = status;

    }
  },
  actions: {
     // 登录
     handleLogin ({ commit }, { userName, password }) {
      userName = userName.trim()
      return new Promise((resolve, reject) => {
        login({
          userName,
          password,
          avatarImgPath: '',
        }).then(res => {
          const data = res.data
          commit('setToken', data.token)
          resolve()
        }).catch(err => {
          reject(err)
        })
      })
    },
        // 退出登录
    handleLogOut ({ state, commit }) {
      return new Promise((resolve, reject) => {
        commit('setHasGetInfo', false)
        commit('setToken', '')
        commit('setAccess', [])
        resolve()
        // 如果你的退出登录无需请求接口，则可以直接使用下面三行代码而无需使用logout调用接口
        // commit('setToken', '')
        // commit('setAccess', [])
        // resolve()
      })
    },
     // 获取用户相关信息
    // getUserInfo ({ state, commit }) {
    //   return new Promise((resolve, reject) => {
    //     try {
    //       getUserInfo(state.token).then(res => {
    //         const data = res.data
    //         commit('setAvatar', data.avatar)
    //         commit('setUserName', data.name)
    //         commit('setUserId', data.user_id)
    //         commit('setAccess', data.access)
    //         commit('setHasGetInfo', true)
    //         resolve(data)
    //       }).catch(err => {
    //         reject(err)
    //       })
    //     } catch (error) {
    //       reject(error)
    //     }
    //   })
    // },

  }
}

export default  user;
