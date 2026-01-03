<template>
  <div class="articles-page">
    <div class="page-header">
      <el-button type="primary" @click="handleCreate">
        <el-icon><Plus /></el-icon>
        新建
      </el-button>
    </div>

    <el-card>
      <div class="filter-bar">
        <el-select
          v-model="filterCategoryId"
          placeholder="筛选分类"
          clearable
          @change="handleCategoryChange"
          style="width: 200px; margin-right: 10px"
        >
          <el-option
            v-for="cat in categories"
            :key="cat.id"
            :label="cat.name"
            :value="cat.id"
          />
        </el-select>
        <el-select
          v-model="filterSubCategoryId"
          placeholder="筛选产品册"
          clearable
          @change="loadData"
          style="width: 200px"
        >
          <el-option
            v-for="sub in filteredSubCategories"
            :key="sub.id"
            :label="sub.name"
            :value="sub.id"
          />
        </el-select>
      </div>

      <el-table :data="articles.data" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="title" label="图片标题" min-width="200" />
        <el-table-column prop="category.name" label="分类名称" width="120" />
        <el-table-column prop="sub_category.name" label="产品册名称" width="120" />
<!--        <el-table-column prop="cover_image" label="封面图" width="120">-->
<!--          <template #default="{ row }">-->
<!--            <el-image-->
<!--              v-if="row.cover_image"-->
<!--              :src="row.cover_image"-->
<!--              :preview-src-list="[row.cover_image]"-->
<!--              fit="cover"-->
<!--              style="width: 60px; height: 60px"-->
<!--            />-->
<!--            <span v-else>-</span>-->
<!--          </template>-->
<!--        </el-table-column>-->
        <el-table-column prop="view_count" label="浏览量" width="100" />
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

      <el-pagination
        v-if="articles.total"
        v-model:current-page="currentPage"
        v-model:page-size="pageSize"
        :total="articles.total"
        layout="total, prev, pager, next, sizes"
        @current-change="loadData"
        @size-change="loadData"
        style="margin-top: 20px; justify-content: flex-end"
      />
    </el-card>

    <!-- 创建/编辑对话框 -->
    <el-dialog
      v-model="dialogVisible"
      :title="dialogTitle"
      width="900px"
      top="5vh"
    >
      <el-form
        ref="formRef"
        :model="formData"
        :rules="rules"
        label-width="120px"
      >
        <el-form-item label="图片集标题" prop="title">
          <el-input v-model="formData.title" placeholder="请输图片集标题" />
        </el-form-item>

        <el-form-item label="分类" prop="category_id">
          <el-select v-model="formData.category_id" placeholder="请选择分类（可选）" clearable>
            <el-option
              v-for="cat in categories"
              :key="cat.id"
              :label="cat.name"
              :value="cat.id"
            />
          </el-select>
        </el-form-item>

        <el-form-item label="产品册" prop="sub_category_id">
          <el-select v-model="formData.sub_category_id" placeholder="请选择产品册（可选）" clearable>
            <el-option
              v-for="sub in allSubCategories"
              :key="sub.id"
              :label="`${sub.category?.name} - ${sub.name}`"
              :value="sub.id"
            />
          </el-select>
        </el-form-item>

<!--        <el-form-item label="封面图" prop="cover_image">-->
<!--          <el-input v-model="formData.cover_image" placeholder="图片URL">-->
<!--            <template #append>-->
<!--              <el-button @click="handleUploadCover">上传</el-button>-->
<!--            </template>-->
<!--          </el-input>-->
<!--          <el-image-->
<!--            v-if="formData.cover_image"-->
<!--            :src="formData.cover_image"-->
<!--            style="width: 150px; margin-top: 10px"-->
<!--          />-->
<!--        </el-form-item>-->

        <el-form-item label="图片集" prop="images">
          <div class="images-list">
            <div v-for="(img, index) in formData.images" :key="index" class="image-item">
              <el-image :src="img" style="width: 100px; height: 100px" fit="cover" />
              <el-button
                type="danger"
                size="small"
                @click="removeImage(index)"
                style="margin-top: 5px"
              >
                删除
              </el-button>
            </div>
            <div class="upload-btn-wrapper">
              <input
                ref="fileInputRef"
                type="file"
                accept="image/jpeg,image/jpg,image/png"
                multiple
                style="display: none"
                @change="handleFilesSelected"
              />
              <el-button @click="triggerFileSelect" type="primary" plain :loading="uploadLoading">
                <el-icon><Plus /></el-icon>
                {{ uploadLoading ? '上传中...' : '添加图片' }}
              </el-button>
            </div>
          </div>
        </el-form-item>

        <el-form-item label="图片集描述" prop="description">
          <el-input
            v-model="formData.description"
            type="textarea"
            :rows="3"
            placeholder="请输入图片集描述"
          />
        </el-form-item>

        <el-form-item label="图片集内容" prop="content">
          <el-input
            v-model="formData.content"
            type="textarea"
            :rows="8"
            placeholder="请输入图片集内容"
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
import { ref, reactive, computed, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
import { getCategoryList, getSubCategoryList } from '@/api/admin/category'
import {
  getArticleList,
  createArticle,
  updateArticle,
  deleteArticle
} from '@/api/admin/article'
import { uploadImage } from '@/api/admin/upload'

const loading = ref(false)
const submitLoading = ref(false)
const uploadLoading = ref(false)
const dialogVisible = ref(false)
const dialogTitle = ref('新建文章')
const formRef = ref(null)
const fileInputRef = ref(null)
const categories = ref([])
const allSubCategories = ref([])
const articles = ref({ data: [], total: 0 })
const filterCategoryId = ref(null)
const filterSubCategoryId = ref(null)
const currentPage = ref(1)
const pageSize = ref(15)

const filteredSubCategories = computed(() => {
  if (!filterCategoryId.value) return allSubCategories.value
  return allSubCategories.value.filter(sub => sub.category_id === filterCategoryId.value)
})

const formData = reactive({
  id: null,
  category_id: null,
  sub_category_id: null,
  title: '',
  slug: '',
  description: '',
  content: '',
  images: [],
  cover_image: '',
  sort_order: 0,
  is_active: true
})

const rules = {
  title: [
    { required: true, message: '请输入图片集标题', trigger: 'blur' }
  ]
}

const loadCategories = async () => {
  try {
    const res = await getCategoryList()
    categories.value = res.data || []
  } catch (error) {
    ElMessage.error('加载分类失败')
  }
}

const loadSubCategories = async () => {
  try {
    const res = await getSubCategoryList()
    allSubCategories.value = res.data || []
  } catch (error) {
    ElMessage.error('加载产品册失败')
  }
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: currentPage.value,
      per_page: pageSize.value
    }
    if (filterCategoryId.value) {
      params.category_id = filterCategoryId.value
    }
    if (filterSubCategoryId.value) {
      params.sub_category_id = filterSubCategoryId.value
    }
    const res = await getArticleList(params)
    articles.value = res.data || { data: [], total: 0 }
  } catch (error) {
    ElMessage.error('加载数据失败')
  } finally {
    loading.value = false
  }
}

const handleCategoryChange = () => {
  filterSubCategoryId.value = null
  loadData()
}

const handleCreate = () => {
  dialogTitle.value = '新建'
  resetForm()
  dialogVisible.value = true
}

const handleEdit = (row) => {
  dialogTitle.value = '编辑'
  Object.assign(formData, { ...row, images: row.images || [] })
  dialogVisible.value = true
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm('确定要删除该图片吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })

    await deleteArticle(row.id)
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
          await updateArticle(formData.id, formData)
          ElMessage.success('更新成功')
        } else {
          await createArticle(formData)
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
const handleFilesSelected = async (event) => {
  const files = Array.from(event.target.files)
  if (files.length === 0) return

  // 验证文件类型和大小
  const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png']
  const maxSize = 5 * 1024 * 1024
  
  for (const file of files) {
    if (!allowedTypes.includes(file.type)) {
      ElMessage.error(`${file.name} 格式不支持，只能上传 JPG、JPEG、PNG 格式的图片`)
      event.target.value = ''
      return
    }
    if (file.size > maxSize) {
      ElMessage.error(`${file.name} 大小超过 5MB`)
      event.target.value = ''
      return
    }
  }

  uploadLoading.value = true
  try {
    for (const file of files) {
      const res = await uploadImage(file)
      formData.images.push(res.data.url)
    }
    ElMessage.success(`成功上传 ${files.length} 张图片`)
    // 清空input，允许重复选择同一文件
    event.target.value = ''
  } catch (error) {
    ElMessage.error('上传失败')
  } finally {
    uploadLoading.value = false
  }
}

const removeImage = (index) => {
  formData.images.splice(index, 1)
}

const resetForm = () => {
  formData.id = null
  formData.category_id = null
  formData.sub_category_id = null
  formData.title = ''
  formData.slug = ''
  formData.description = ''
  formData.content = ''
  formData.images = []
  formData.cover_image = ''
  formData.sort_order = 0
  formData.is_active = true
}

onMounted(() => {
  loadCategories()
  loadSubCategories()
  loadData()
})
</script>

<style lang="scss" scoped>
.articles-page {
  .page-header {
    margin-bottom: 20px;
  }

  .filter-bar {
    margin-bottom: 20px;
  }

  .images-list {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;

    .image-item {
      display: flex;
      flex-direction: column;
      align-items: center;
    }
  }
}
</style>

