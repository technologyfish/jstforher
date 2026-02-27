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
        <el-table-column prop="id" label="ID" width="60" />

        <!-- 姓名：优先显示 first_name+last_name，兼容旧数据的 name 字段 -->
        <el-table-column label="First Name" width="120">
          <template #default="{ row }">
            {{ row.first_name || (row.name ? row.name.split(' ')[0] : '-') }}
          </template>
        </el-table-column>
        <el-table-column label="Last Name" width="120">
          <template #default="{ row }">
            {{ row.last_name || (row.name ? row.name.split(' ').slice(1).join(' ') : '-') || '-' }}
          </template>
        </el-table-column>

        <el-table-column prop="email" label="Email" width="200" />
        <el-table-column prop="business_info" label="Business Name / Website" width="200" />
        <el-table-column prop="location" label="Location" width="140">
          <template #default="{ row }">{{ row.location || '-' }}</template>
        </el-table-column>
        <el-table-column prop="estimated_quantity" label="Est. Quantity" width="130">
          <template #default="{ row }">{{ row.estimated_quantity || '-' }}</template>
        </el-table-column>
        <el-table-column prop="message" label="Message" min-width="180">
          <template #default="{ row }">
            <span class="cell-ellipsis">{{ row.message || '-' }}</span>
          </template>
        </el-table-column>

        <el-table-column prop="is_read" label="状态" width="80">
          <template #default="{ row }">
            <el-tag :type="row.is_read ? 'success' : 'warning'">
              {{ row.is_read ? '已读' : '未读' }}
            </el-tag>
          </template>
        </el-table-column>
        <el-table-column label="提交时间" width="175">
          <template #default="{ row }">
            {{ formatDate(row.created_at) }}
          </template>
        </el-table-column>
        <el-table-column label="操作" width="140" fixed="right">
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
    <el-dialog v-model="dialogVisible" title="留言详情" width="560px">
      <el-descriptions v-if="currentItem" :column="1" border>
        <el-descriptions-item label="ID">{{ currentItem.id }}</el-descriptions-item>

        <el-descriptions-item label="First Name">
          {{ currentItem.first_name || (currentItem.name ? currentItem.name.split(' ')[0] : '-') }}
        </el-descriptions-item>
        <el-descriptions-item label="Last Name">
          {{ currentItem.last_name || (currentItem.name ? currentItem.name.split(' ').slice(1).join(' ') : '-') || '-' }}
        </el-descriptions-item>

        <el-descriptions-item label="Email">{{ currentItem.email }}</el-descriptions-item>

        <el-descriptions-item label="Business Name / Website">
          {{ currentItem.business_info || '-' }}
        </el-descriptions-item>

        <el-descriptions-item label="Location">
          {{ currentItem.location || '-' }}
        </el-descriptions-item>

        <el-descriptions-item label="Est. Order Quantity">
          {{ currentItem.estimated_quantity || '-' }}
        </el-descriptions-item>

        <el-descriptions-item label="状态">
          <el-tag :type="currentItem.is_read ? 'success' : 'warning'">
            {{ currentItem.is_read ? '已读' : '未读' }}
          </el-tag>
        </el-descriptions-item>

        <el-descriptions-item label="提交时间">
          {{ formatDate(currentItem.created_at) }}
        </el-descriptions-item>

        <el-descriptions-item label="Message">
          <div style="white-space: pre-wrap; max-height: 300px; overflow-y: auto; word-break: break-all;">
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

const formatDate = (dateString) => {
  if (!dateString) return '-'
  const date = new Date(dateString)
  const pad = (n) => String(n).padStart(2, '0')
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}:${pad(date.getSeconds())}`
}

const loadData = async () => {
  loading.value = true
  try {
    const params = { page: currentPage.value, per_page: pageSize.value }
    if (filterIsRead.value !== null) params.is_read = filterIsRead.value
    const res = await getContactFormList(params)
    contactForms.value = res.data || { data: [], total: 0 }
  } catch {
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
    if (!row.is_read) {
      await markAsRead(row.id)
      loadData()
    }
  } catch {
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
    if (error !== 'cancel') ElMessage.error('删除失败')
  }
}

onMounted(loadData)
</script>

<style lang="scss" scoped>
.contact-forms-page {
  .filter-bar {
    margin-bottom: 20px;
  }
}

// 列表单元格内容截断，最多显示两行
.cell-ellipsis {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  word-break: break-all;
  line-height: 1.5;
}
</style>
