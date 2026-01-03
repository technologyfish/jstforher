import request from '@/utils/request'

/**
 * 提交订阅
 */
export function subscribeNewsletter(data) {
  return request({
    url: '/api/newsletter/subscribe',
    method: 'post',
    data
  })
}

