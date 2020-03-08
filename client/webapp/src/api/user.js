import axios from '@/libs/api.request'
import { prefixApi } from '@/utils/env'
// axios.request() 一个axios对象
export const login = ({ userName, password }) => {
  const data = {
    userName,
    password
  }
  return axios.request().post(prefixApi + '/login', data)


}

// export const logout = (token) => {
//     return axios.request({
//       url: 'logout',
//       method: 'post'
//     })
// }

export const getUserInfo = (token) => {
  return axios.request({
    url: 'get_info',
    params: {
      token
    },
    method: 'get'
  })
}