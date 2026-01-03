import request from '@/utils/request'

// 获取文章列表
export function getArticleList(params) {
  return request({
    url: '/api/admin/articles',
    method: 'get',
    params
  })
}

// 创建文章
export function createArticle(data) {
  return request({
    url: '/api/admin/articles',
    method: 'post',
    data
  })
}

// 获取文章详情
export function getArticleDetail(id) {
  return request({
    url: `/api/admin/articles/${id}`,
    method: 'get'
  })
}

// 更新文章
export function updateArticle(id, data) {
  return request({
    url: `/api/admin/articles/${id}`,
    method: 'put',
    data
  })
}

// 删除文章
export function deleteArticle(id) {
  return request({
    url: `/api/admin/articles/${id}`,
    method: 'delete'
  })
}


