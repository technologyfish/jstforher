<template>
  <div class="contact-forms-page">
    <el-card>
      <div class="filter-bar">
        <el-select
          v-model="filterIsRead"
          placeholder="筛选状态"
          clearable
          @change="loadData"
          style="width: 150px"
        >
          <el-option label="未读" :value="false" />
          <el-option label="已读" :value="true" />
        </el-select>
      </div>

      <el-table :data="contactForms.data" v-loading="loading" stripe>
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="name" label="姓名" width="150" />
        <el-table-column prop="email" label="邮箱" width="220" />
        <el-table-column prop="message" label="留言内容" min-width="250" show-overflow-tooltip />
        <el-table-column prop="is_read" label="状态" width="100">
          <template #default="{ row }">
            <el-tag :type="row.is_read ? 'success' : 'warning'">
              {{ row.is_read ? '已读' : '未读' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="提交时间" width="180">
          <template #default="{ row }">
            {{ formatDate(row.created_at) }}
          </template>
        </el-table-column>
        <el-table-column label="操作" width="150" fixed="right">
          <template #default="{ row }">
            <el-button type="primary" size="small" @click="handleView(row)">查看</el-button>
            <el-button type="danger" size="small" @click="handleDelete(row)">删除</el-button>
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        v-if="contactForms.total"
        v-model:current-page="currentPage"
        v-model:page-size="pageSize"
        :total="contactForms.total"
        layout="total, prev, pager, next, sizes"
        @current-change="loadData"
        @size-change="loadData"
        style="margin-top: 20px; justify-content: flex-end"
      />
    </el-card>

    <!-- 查看详情对话框 -->
    <el-dialog
      v-model="dialogVisible"
      title="留言详情"
      width="600px"
    >
      <el-descriptions v-if="currentItem" :column="1" border>
        <el-descriptions-item label="ID">{{ currentItem.id }}</el-descriptions-item>
        <el-descriptions-item label="姓名">{{ currentItem.name }}</el-descriptions-item>
        <el-descriptions-item label="邮箱">{{ currentItem.email }}</el-descriptions-item>
        <el-descriptions-item label="状态">
          <el-tag :type="currentItem.is_read ? 'success' : 'warning'">
            {{ currentItem.is_read ? '已读' : '未读' }}
          </el-tag>
        </el-descriptions-item>
        <el-descriptions-item label="提交时间">{{ formatDate(currentItem.created_at) }}</el-descriptions-item>
        <el-descriptions-item label="留言内容">
          <div style="white-space: pre-wrap; max-height: 300px; overflow-y: auto;">
            {{ currentItem.message || '-' }}
          </div>
        </el-descriptions-item>
      </el-descriptions>

      <template #footer>
        <el-button @click="dialogVisible = false">关闭</el-button>
      </template>
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import {
  getContactFormList,
  getContactFormDetail,
  markAsRead,
  deleteContactForm
} from '@/api/admin/contactForm'

const loading = ref(false)
const dialogVisible = ref(false)
const contactForms = ref({ data: [], total: 0 })
const currentItem = ref(null)
const filterIsRead = ref(null)
const currentPage = ref(1)
const pageSize = ref(15)

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

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      page: currentPage.value,
      per_page: pageSize.value
    }
    if (filterIsRead.value !== null) {
      params.is_read = filterIsRead.value
    }
    const res = await getContactFormList(params)
    contactForms.value = res.data || { data: [], total: 0 }
  } catch (error) {
    ElMessage.error('加载数据失败')
  } finally {
    loading.value = false
  }
}

const handleView = async (row) => {
  try {
    const res = await getContactFormDetail(row.id)
    currentItem.value = res.data
    dialogVisible.value = true
    
    // 自动标记为已读
    if (!row.is_read) {
      await markAsRead(row.id)
      loadData()
    }
  } catch (error) {
    console.error('加载详情失败:', error)
    ElMessage.error('加载详情失败')
  }
}

const handleDelete = async (row) => {
  try {
    await ElMessageBox.confirm('确定要删除该留资吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })

    await deleteContactForm(row.id)
    ElMessage.success('删除成功')
    loadData()
  } catch (error) {
    if (error !== 'cancel') {
      ElMessage.error('删除失败')
    }
  }
}

onMounted(() => {
  loadData()
})
</script>

<style lang="scss" scoped>
.contact-forms-page {
  .filter-bar {
    margin-bottom: 20px;
  }
}
</style>

