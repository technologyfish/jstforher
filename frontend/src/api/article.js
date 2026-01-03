import request from '@/utils/request'

// 获取文章列表
export function getArticles(params) {
  return request({
    url: '/api/articles',
    method: 'get',
    params
  })
}

// 获取单个文章详情
export function getArticle(slug) {
  return request({
    url: `/api/articles/${slug}`,
    method: 'get'
  })
}


