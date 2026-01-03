import request from '@/utils/request'

/**
 * 提交联系表单
 */
export function submitContactForm(data) {
  return request({
    url: '/api/contact',
    method: 'post',
    data
  })
}
