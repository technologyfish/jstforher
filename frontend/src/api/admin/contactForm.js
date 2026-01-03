import request from '@/utils/request'

// 获取表单留资列表
export function getContactFormList(params) {
  return request({
    url: '/api/admin/contact-forms',
    method: 'get',
    params
  })
}

// 获取表单留资详情
export function getContactFormDetail(id) {
  return request({
    url: `/api/admin/contact-forms/${id}`,
    method: 'get'
  })
}

// 标记为已读
export function markAsRead(id) {
  return request({
    url: `/api/admin/contact-forms/${id}/mark-read`,
    method: 'post'
  })
}

// 批量标记为已读
export function batchMarkAsRead(ids) {
  return request({
    url: '/api/admin/contact-forms/batch-mark-read',
    method: 'post',
    data: { ids }
  })
}

// 删除表单留资
export function deleteContactForm(id) {
  return request({
    url: `/api/admin/contact-forms/${id}`,
    method: 'delete'
  })
}


