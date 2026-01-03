import request from '@/utils/request'

// 管理员登录
export function login(data) {
  return request({
    url: '/api/admin/login',
    method: 'post',
    data
  })
}

// 获取当前管理员信息
export function getAdminInfo() {
  return request({
    url: '/api/admin/me',
    method: 'get'
  })
}

// 登出
export function logout() {
  return request({
    url: '/api/admin/logout',
    method: 'post'
  })
}


