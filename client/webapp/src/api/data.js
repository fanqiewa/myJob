
import axios from '@/libs/api.request'
export const saveErrorLogger = info => {
    return axios.request({
      url: 'save_error_logger',
      data: info,
      method: 'post'
    })
  }