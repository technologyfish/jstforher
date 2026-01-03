import request from '@/utils/request'

// 获取栏目列表
export function getCategoryList(params) {
  return request({
    url: '/admin/categories',
    method: 'get',
    params
  })
}

// 创建栏目
export function createCategory(data) {
  return request({
    url: '/admin/categories',
    method: 'post',
    data
  })
}

// 获取栏目详情
export function getCategoryDetail(id) {
  return request({
    url: `/admin/categories/${id}`,
    method: 'get'
  })
}

// 更新栏目
export function updateCategory(id, data) {
  return request({
    url: `/admin/categories/${id}`,
    method: 'put',
    data
  })
}

// 删除栏目
export function deleteCategory(id) {
  return request({
    url: `/admin/categories/${id}`,
    method: 'delete'
  })
}

// 获取二级栏目列表
export function getSubCategoryList(params) {
  return request({
    url: '/admin/sub-categories',
    method: 'get',
    params
  })
}

// 创建二级栏目
export function createSubCategory(data) {
  return request({
    url: '/admin/sub-categories',
    method: 'post',
    data
  })
}

// 获取二级栏目详情
export function getSubCategoryDetail(id) {
  return request({
    url: `/admin/sub-categories/${id}`,
    method: 'get'
  })
}

// 更新二级栏目
export function updateSubCategory(id, data) {
  return request({
    url: `/admin/sub-categories/${id}`,
    method: 'put',
    data
  })
}

// 删除二级栏目
export function deleteSubCategory(id) {
  return request({
    url: `/admin/sub-categories/${id}`,
    method: 'delete'
  })
}


