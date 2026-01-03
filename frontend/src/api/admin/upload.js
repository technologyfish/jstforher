import request from '@/utils/request'

// 上传单个图片
export function uploadImage(file) {
  const formData = new FormData()
  formData.append('file', file)
  
  return request({
    url: '/api/admin/upload/image',
    method: 'post',
    data: formData,
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}

// 批量上传图片
export function uploadImages(files) {
  const formData = new FormData()
  files.forEach(file => {
    formData.append('files[]', file)
  })
  
  return request({
    url: '/api/admin/upload/images',
    method: 'post',
    data: formData,
    headers: {
      'Content-Type': 'multipart/form-data'
    }
  })
}


