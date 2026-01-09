<template>
  <el-container class="admin-layout">
    <el-aside width="250px" class="sidebar">
      <div class="logo">
        <h2>JstForHer Admin</h2>
      </div>
      <el-menu
        :default-active="currentRoute"
        router
        class="sidebar-menu"
      >
        <el-menu-item index="/fanggangrong/categories">
          <el-icon><List /></el-icon>
          <span>分类管理</span>
        </el-menu-item>
        <el-menu-item index="/fanggangrong/sub-categories">
          <el-icon><Files /></el-icon>
          <span>产品册管理</span>
        </el-menu-item>
        <el-menu-item index="/fanggangrong/articles">
          <el-icon><Document /></el-icon>
          <span>图片集管理</span>
        </el-menu-item>
        <el-menu-item index="/fanggangrong/contact-forms">
          <el-icon><Message /></el-icon>
          <span>表单留资</span>
        </el-menu-item>
        <el-menu-item index="/fanggangrong/newsletter-subscriptions">
          <el-icon><Bell /></el-icon>
          <span>订阅列表</span>
        </el-menu-item>
        <el-menu-item index="/fanggangrong/products">
          <el-icon><ShoppingBag /></el-icon>
          <span>产品管理</span>
        </el-menu-item>
      </el-menu>
    </el-aside>

    <el-container>
      <el-header class="header">
        <div class="header-left">
          <h3>{{ pageTitle }}</h3>
        </div>
        <div class="header-right">
          <span class="admin-name">{{ adminName }}</span>
          <el-button @click="handleLogout" type="danger" size="small">Logout</el-button>
        </div>
      </el-header>

      <el-main class="main-content">
        <router-view />
      </el-main>
    </el-container>
  </el-container>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ElMessage, ElMessageBox } from 'element-plus'
import { List, Files, Document, Message, Bell, ShoppingBag } from '@element-plus/icons-vue'

const router = useRouter()
const route = useRoute()

const currentRoute = computed(() => route.path)

const adminName = computed(() => {
  const adminInfo = localStorage.getItem('admin_info')
  if (adminInfo) {
    const admin = JSON.parse(adminInfo)
    return admin.name || admin.username
  }
  return 'Admin'
})

const pageTitle = computed(() => {
  const titles = {
    '/fanggangrong/categories': '分类管理',
    '/fanggangrong/sub-categories': '产品册管理',
    '/fanggangrong/articles': '图片集管理',
    '/fanggangrong/contact-forms': '表单留资管理',
    '/fanggangrong/newsletter-subscriptions': '订阅列表管理',
    '/fanggangrong/products': '产品管理'
  }
  return titles[route.path] || 'Dashboard'
})

const handleLogout = async () => {
  try {
    await ElMessageBox.confirm('确定要退出登录吗？', '提示', {
      confirmButtonText: '确定',
      cancelButtonText: '取消',
      type: 'warning'
    })
    
    // 直接清除本地存储，不调用后端API
    localStorage.removeItem('admin_token')
    localStorage.removeItem('admin_info')
    ElMessage.success('已退出登录')
    router.push('/fanggangrong/login')
  } catch (error) {
    // 用户取消
  }
}
</script>

<style lang="scss" scoped>
.admin-layout {
  height: 100vh;
}

.sidebar {
  background: #2c3e50;
  color: $white;

  .logo {
    padding: 20px;
    text-align: center;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);

    h2 {
      color: $white;
      font-size: 20px;
      font-weight: 600;
      margin: 0;
    }
  }

  .sidebar-menu {
    border: none;
    background: #2c3e50;

    :deep(.el-menu-item) {
      color: rgba(255, 255, 255, 0.8);

      &:hover {
        background: rgba(255, 255, 255, 0.1);
        color: $white;
      }

      &.is-active {
        background: #409eff;
        color: $white;
      }
    }
  }
}

.header {
  background: $white;
  border-bottom: 1px solid $border-color;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 20px;

  .header-left {
    h3 {
      margin: 0;
      font-size: 20px;
      font-weight: 500;
    }
  }

  .header-right {
    display: flex;
    align-items: center;
    gap: 15px;

    .admin-name {
      color: $text-light;
      font-size: 14px;
    }
  }
}

.main-content {
  background: #f5f7fa;
  padding: 20px;
  overflow-y: auto;
}
</style>

