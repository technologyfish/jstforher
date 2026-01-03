import request from '@/utils/request'

// 获取所有栏目
export function getCategories() {
  return request({
    url: '/api/categories',
    method: 'get'
  })
}

// 获取单个栏目详情
export function getCategory(slug) {
  return request({
    url: `/api/categories/${slug}`,
    method: 'get'
  })
}

// 获取产品册列表
export function getSubCategories(params) {
  return request({
    url: '/api/sub-categories',
    method: 'get',
    params
  })
}

// 获取产品册详情
export function getSubCategory(id) {
  return request({
    url: `/api/sub-categories/${id}`,
    method: 'get'
  })
}

