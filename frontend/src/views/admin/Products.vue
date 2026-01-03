<template>
  <div class="products-page">
    <div class="page-header">
      <el-button type="primary" @click="handleCreate">
        <el-icon><Plus /></el-icon>
        新建产品
      </el-button>
    </div>

    <el-card>
      <!-- 筛选区域 -->
      <el-form :inline="true" :model="filters" class="filter-form">
        <el-form-item label="产品名称">
          <el-input 
            v-model="filters.keyword" 
            placeholder="搜索产品名称" 
            clearable 
            @clear="handleSearch"
            style="width: 260px"
          />
        </el-form-item>
        <el-form-item label="状态">
          <el-select 
            v-model="filters.is_active" 
            placeholder="全部" 
            clearable
            style="width: 160px"
          >
            <el-option label="全部" value="" />
            <el-option label="启用" :value="1" />
            <el-option label="禁用" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSearch">搜索</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <!-- 数据表格 -->
      <el-table :data="tableData" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="cover_image" label="封面图" width="120">
          <template #default="{ row }">
            <el-image
              v-if="row.cover_image"
              :src="row.cover_image"
              :preview-src-list="[row.cover_image]"
              fit="cover"
              style="width: 60px; height: 60px"
            />
            <span v-else>-</span>
          </template>
        </el-table-column>
        <el-table-column prop="name" label="产品名称" min-width="200" />
        <el-table-column prop="description" label="产品描述" min-width="300" show-overflow-tooltip />
        <el-table-column prop="sort_order" label="排序" width="100" />
        <el-table-column prop="is_active" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.is_active ? 'success' : 'danger'">
              {{ row.is_active ? '启用' : '禁用' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" size="small" @click="handleEdit(row)">编辑</el-button>
            <el-button type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <!-- 分页 -->
      <el-pagination
        v-model:current-page="pagination.current"
        v-model:page-size="pagination.pageSize"
        :total="pagination.total"
        :page-sizes="[15, 30, 50, 100]"
        layout="total, sizes, prev, pager, next, jumper"
        @size-change="handleSearch"
        @current-change="handleSearch"
        class="pagination"
      />
    </el-card>

    <!-- 创建/编辑对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="dialogTitle"
      width="700px"
    >
      <el-form
        ref="formRef"
        :model="formData"
        :rules="rules"
        label-width="120px"
      >
        <el-form-item label="产品名称" prop="name">
          <el-input v-model="formData.name" placeholder="请输入产品名称" />
        </el-form-item>

        <el-form-item label="封面图" prop="cover_image">
          <div class="cover-image-upload">
            <input
              ref="fileInputRef"
              type="file"
              accept="image/jpeg,image/jpg,image/png"
              style="display: none"
              @change="handleFileSelected"
            />
            <el-button @click="triggerFileSelect" :loading="uploadLoading">
              {{ uploadLoading ? '上传中...' : '上传' }}
            </el-button>
            <el-input 
              v-model="formData.cover_image" 
              placeholder="图片URL" 
              style="margin-top: 10px"
            />
            <div v-if="formData.cover_image" class="image-preview">
              <el-image
                :src="formData.cover_image"
                style="width: 200px; height: 200px; margin-top: 10px"
                fit="cover"
              />
              <el-button
                type="danger"
                size="small"
                @click="formData.cover_image = ''"
                style="margin-top: 5px"
              >
                删除图片
              </el-button>
            </div>
          </div>
        </el-form-item>

        <el-form-item label="产品描述" prop="description">
          <el-input
            v-model="formData.description"
            type="textarea"
            :rows="5"
            placeholder="请输入产品描述"
          />
        </el-form-item>

        <el-form-item label="排序" prop="sort_order">
          <el-input-number v-model="formData.sort_order" :min="0" />
          <span style="margin-left: 10px; color: #999; font-size: 12px">数字越小越靠前</span>
        </el-form-item>

        <el-form-item label="状态" prop="is_active">
          <el-switch v-model="formData.is_active" />
        </el-form-item>
      </el-form>

      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" :loading="submitLoading" @click="handleSubmit">
          确定
        </el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
import request from '@/utils/request'
import { uploadImage } from '@/api/admin/upload'

const loading = ref(false)
const submitLoading = ref(false)
const uploadLoading = ref(false)
const dialogVisible = ref(false)
const dialogTitle = ref('新建产品')
const formRef = ref(null)
const fileInputRef = ref(null)
const tableData = ref([])

const filters = reactive({
  keyword: '',
  is_active: ''
})

const pagination = reactive({
  current: 1,
  pageSize: 15,
  total: 0
})

const formData = reactive({
  id: null,
  name: '',
  description: '',
  cover_image: '',
  sort_order: 0,
  is_active: true
})

const rules = {
  name: [
    { required: true, message: '请输入产品名称', trigger: 'blur' }
  ]
}

// 获取列表
const fetchList = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.current,
      per_page: pagination.pageSize
    }
    
    // 只添加有值的参数
    if (filters.keyword) {
      params.keyword = filters.keyword
    }
    if (filters.is_active !== '') {
      params.is_active = filters.is_active
    }
    
    const res = await request.get('/admin/products', { params })
    tableData.value = res.data.data
    pagination.total = res.data.total
  } catch (error) {
    ElMessage.error('加载数据失败')
  } finally {
    loading.value = false
  }
}

// 搜索
const handleSearch = () => {
  pagination.current = 1
  fetchList()
}

// 重置
const handleReset = () => {
  filters.keyword = ''
  filters.is_active = ''
  handleSearch()
}

// 新建
const handleCreate = () => {
  dialogTitle.value = '新建产品'
  resetForm()
  dialogVisible.value = true
}

// 编辑
const handleEdit = (row) => {
  dialogTitle.value = '编辑产品'
  Object.assign(formData, row)
  dialogVisible.value = true
}

// 删除
const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm('确定要删除该产品吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })

    await request.delete(`/admin/products/${row.id}`)
    ElMessage.success('删除成功')
    fetchList()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
    }
  }
}

// 提交
const handleSubmit = async () => {
  if (!formRef.value) return

  await formRef.value.validate(async (valid) => {
    if (valid) {
      submitLoading.value = true
      try {
        if (formData.id) {
          await request.put(`/admin/products/${formData.id}`, formData)
          ElMessage.success('更新成功')
        } else {
          await request.post('/admin/products', formData)
          ElMessage.success('创建成功')
        }
        dialogVisible.value = false
        fetchList()
      } catch (error) {
        ElMessage.error('操作失败')
      } finally {
        submitLoading.value = false
      }
    }
  })
}

// 触发文件选择
const triggerFileSelect = () => {
  fileInputRef.value?.click()
}

// 处理文件选择并直接上传
const handleFileSelected = async (event) => {
  const file = event.target.files[0]
  if (!file) return

  // 验证文件类型
  const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png']
  if (!allowedTypes.includes(file.type)) {
    ElMessage.error('只能上传 JPG、JPEG、PNG 格式的图片')
    event.target.value = ''
    return
  }

  // 验证文件大小（限制为5MB）
  const maxSize = 5 * 1024 * 1024
  if (file.size > maxSize) {
    ElMessage.error('图片大小不能超过 5MB')
    event.target.value = ''
    return
  }

  uploadLoading.value = true
  try {
    const res = await uploadImage(file)
    formData.cover_image = res.data.url
    ElMessage.success('上传成功')
    // 清空input，允许重复选择同一文件
    event.target.value = ''
  } catch (error) {
    ElMessage.error('上传失败')
  } finally {
    uploadLoading.value = false
  }
}

// 重置表单
const resetForm = () => {
  formData.id = null
  formData.name = ''
  formData.description = ''
  formData.cover_image = ''
  formData.sort_order = 0
  formData.is_active = true
}

onMounted(() => {
  fetchList()
})
</script>

<style lang="scss" scoped>
.products-page {
  .page-header {
    margin-bottom: 20px;
  }

  .filter-form {
    margin-bottom: 20px;
  }

  .pagination {
    margin-top: 20px;
    justify-content: flex-end;
  }

  .cover-image-upload {
    width: 100%;
    
    .image-preview {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }
  }
}
</style>

