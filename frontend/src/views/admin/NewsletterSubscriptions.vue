<template>
  <div class="newsletter-subscriptions-page">
    <el-card>
      <template #header>
        <div class="card-header">
          <span>邮件订阅列表</span>
        </div>
      </template>

      <!-- 筛选区域 -->
      <el-form :inline="true" :model="filters" class="filter-form">
        <el-form-item label="邮箱">
          <el-input 
            v-model="filters.email" 
            placeholder="搜索邮箱" 
            clearable 
            @clear="handleSearch"
            style="width: 260px"
          />
        </el-form-item>
        <el-form-item label="状态">
          <el-select 
            v-model="filters.status" 
            placeholder="全部" 
            clearable
            style="width: 160px"
          >
            <el-option label="全部" value="" />
            <el-option label="待处理" :value="0" />
            <el-option label="已处理" :value="1" />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSearch">搜索</el-button>
          <el-button @click="handleReset">重置</el-button>
        </el-form-item>
      </el-form>

      <!-- 数据表格 -->
      <el-table 
        :data="tableData" 
        v-loading="loading"
      >
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="email" label="邮箱" />
        <el-table-column label="状态" width="250">
          <template #default="{ row }">
            <el-tag :type="row.status === 1 ? 'success' : 'warning'">
              {{ row.status === 1 ? '已处理' : '待处理' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="提交时间" width="250">
          <template #default="{ row }">
            {{ formatDate(row.created_at) }}
          </template>
        </el-table-column>
        <el-table-column label="操作" width="200" fixed="right">
          <template #default="{ row }">
            <el-button link type="primary" @click="handleEdit(row)">编辑</el-button>
            <el-button link type="danger" @click="handleDelete(row)">删除</el-button>
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

    <!-- 编辑对话框 -->
    <el-dialog v-model="dialogVisible" title="编辑订阅" width="500px">
      <el-form :model="editForm" label-width="120px">
        <el-form-item label="邮箱">
          <el-input v-model="editForm.email" disabled />
        </el-form-item>
        <el-form-item label="状态">
          <el-select v-model="editForm.status" placeholder="选择状态">
            <el-option label="待处理" :value="0" />
            <el-option label="已处理" :value="1" />
          </el-select>
        </el-form-item>
        <el-form-item label="管理员备注">
          <el-input 
            v-model="editForm.admin_note" 
            type="textarea" 
            :rows="4"
            placeholder="添加备注..."
          />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="dialogVisible = false">取消</el-button>
        <el-button type="primary" @click="handleSave" :loading="saveLoading">保存</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import request from '@/utils/request'

const loading = ref(false)
const saveLoading = ref(false)
const dialogVisible = ref(false)
const tableData = ref([])

const filters = reactive({
  email: '',
  status: ''
})

const pagination = reactive({
  current: 1,
  pageSize: 15,
  total: 0
})

const editForm = reactive({
  id: null,
  email: '',
  status: 0,
  admin_note: ''
})

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

// 获取列表
const fetchList = async () => {
  loading.value = true
  try {
    const params = {
      page: pagination.current,
      per_page: pagination.pageSize,
      email: filters.email,
      status: filters.status
    }
    const res = await request.get('/admin/newsletter-subscriptions', { params })
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
  filters.email = ''
  filters.status = ''
  handleSearch()
}

// 编辑
const handleEdit = (row) => {
  editForm.id = row.id
  editForm.email = row.email
  editForm.status = row.status
  editForm.admin_note = row.admin_note || ''
  dialogVisible.value = true
}

// 保存
const handleSave = async () => {
  saveLoading.value = true
  try {
    await request.put(`/admin/newsletter-subscriptions/${editForm.id}/status`, {
      status: editForm.status,
      admin_note: editForm.admin_note
    })
    ElMessage.success('更新成功')
    dialogVisible.value = false
    fetchList()
  } catch (error) {
    ElMessage.error('更新失败')
  } finally {
    saveLoading.value = false
  }
}

// 删除
const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm('确定要删除此订阅吗？', '警告', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    
    await request.delete(`/admin/newsletter-subscriptions/${row.id}`)
    ElMessage.success('删除成功')
    fetchList()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
    }
  }
}

onMounted(() => {
  fetchList()
})
</script>

<style lang="scss" scoped>
.newsletter-subscriptions-page {
  //padding: 20px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.filter-form {
  margin-bottom: 20px;
}

.pagination {
  margin-top: 20px;
  justify-content: flex-end;
}
</style>

