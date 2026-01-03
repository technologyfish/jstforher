<template>
  <div class="sub-categories-page">
    <div class="page-header">
      <el-button type="primary" @click="handleCreate">
        <el-icon><Plus /></el-icon>
        新建产品册
      </el-button>
    </div>

    <el-card>
      <div class="filter-bar">
        <el-select
          v-model="filterCategoryId"
          placeholder="筛选分类"
          clearable
          @change="loadData"
          style="width: 200px"
        >
          <el-option
            v-for="cat in categories"
            :key="cat.id"
            :label="cat.name"
            :value="cat.id"
          />
        </el-select>
      </div>

      <el-table :data="subCategories" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="category.name" label="所属分类" width="150" />
        <el-table-column prop="name" label="产品册名称" />
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
        <el-table-column prop="sort_order" label="排序" width="100" />
        <el-table-column prop="is_active" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.is_active ? 'success' : 'danger'">
              {{ row.is_active ? '启用' : '禁用' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column prop="created_at" label="创建时间" width="180">
          <template #default="{ row }">
            {{ formatDate(row.created_at) }}
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" size="small" @click="handleEdit(row)">编辑</el-button>
            <el-button type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>

    <!-- 创建/编辑对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="dialogTitle"
      width="600px"
    >
      <el-form
        ref="formRef"
        :model="formData"
        :rules="rules"
        label-width="120px"
      >
        <el-form-item label="所属分类" prop="category_id">
          <el-select v-model="formData.category_id" placeholder="请选择分类">
            <el-option
              v-for="cat in categories"
              :key="cat.id"
              :label="cat.name"
              :value="cat.id"
            />
          </el-select>
        </el-form-item>

        <el-form-item label="产品册名称" prop="name">
          <el-input v-model="formData.name" placeholder="请输入产品册名称" />
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
                style="width: 150px; height: 150px; margin-top: 10px"
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

        <el-form-item label="产品册描述" prop="description">
          <el-input
            v-model="formData.description"
            type="textarea"
            :rows="4"
            placeholder="请输入产品册描述"
          />
        </el-form-item>

        <el-form-item label="排序" prop="sort_order">
          <el-input-number v-model="formData.sort_order" :min="0" />
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
import { getCategoryList } from '@/api/admin/category'
import {
  getSubCategoryList,
  createSubCategory,
  updateSubCategory,
  deleteSubCategory
} from '@/api/admin/category'
import { uploadImage } from '@/api/admin/upload'

const loading = ref(false)
const submitLoading = ref(false)
const uploadLoading = ref(false)
const dialogVisible = ref(false)
const dialogTitle = ref('新建二级栏目')
const formRef = ref(null)
const fileInputRef = ref(null)
const categories = ref([])
const subCategories = ref([])
const filterCategoryId = ref(null)

const formData = reactive({
  id: null,
  category_id: null,
  name: '',
  description: '',
  cover_image: '',
  sort_order: 0,
  is_active: true
})

const rules = {
  category_id: [
    { required: true, message: '请选择分类', trigger: 'change' }
  ],
  name: [
    { required: true, message: '请输入产品册名称', trigger: 'blur' }
  ]
}

// 格式化日期
const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  const seconds = String(date.getSeconds()).padStart(2, '0')
  return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`
}

const loadCategories = async () => {
  try {
    const res = await getCategoryList()
    categories.value = res.data || []
  } catch (error) {
    ElMessage.error('加载分类失败')
  }
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {}
    if (filterCategoryId.value) {
      params.category_id = filterCategoryId.value
    }
    const res = await getSubCategoryList(params)
    subCategories.value = res.data || []
  } catch (error) {
    ElMessage.error('加载数据失败')
  } finally {
    loading.value = false
  }
}

const handleCreate = () => {
  dialogTitle.value = '新建产品册'
  resetForm()
  dialogVisible.value = true
}

const handleEdit = (row) => {
  dialogTitle.value = '编辑产品册'
  Object.assign(formData, row)
  dialogVisible.value = true
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm('确定要删除该产品册吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })

    await deleteSubCategory(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
    }
  }
}

const handleSubmit = async () => {
  if (!formRef.value) return

  await formRef.value.validate(async (valid) => {
    if (valid) {
      submitLoading.value = true
      try {
        if (formData.id) {
          await updateSubCategory(formData.id, formData)
          ElMessage.success('更新成功')
        } else {
          await createSubCategory(formData)
          ElMessage.success('创建成功')
        }
        dialogVisible.value = false
        loadData()
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

const resetForm = () => {
  formData.id = null
  formData.category_id = null
  formData.name = ''
  formData.description = ''
  formData.cover_image = ''
  formData.sort_order = 0
  formData.is_active = true
}

onMounted(() => {
  loadCategories()
  loadData()
})
</script>

<style lang="scss" scoped>
.sub-categories-page {
  .page-header {
    margin-bottom: 20px;
  }

  .filter-bar {
    margin-bottom: 20px;
  }

  .cover-image-upload {
    .image-preview {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }
  }
}
</style>

