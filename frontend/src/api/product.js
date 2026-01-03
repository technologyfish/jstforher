import request from '@/utils/request'

// 获取产品列表（前端用户接口）
export function getProductList(params) {
  return request({
    url: '/api/products',
    method: 'get',
    params
  })
}

// 获取产品详情
export function getProductDetail(id) {
  return request({
    url: `/api/products/${id}`,
    method: 'get'
  })
}

