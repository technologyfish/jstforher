<template>
  <div class="login-page">
    <div class="login-container">
      <div class="login-box">
        <el-form
          ref="formRef"
          :model="formData"
          :rules="rules"
          label-position="top"
          class="login-form"
        >
          <el-form-item label="Username" prop="username">
            <el-input
              v-model="formData.username"
              placeholder="Enter username"
              size="large"
            >
              <template #prefix>
                <el-icon><User /></el-icon>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item label="Password" prop="password">
            <el-input
              v-model="formData.password"
              type="password"
              placeholder="Enter password"
              size="large"
              @keyup.enter="handleLogin"
            >
              <template #prefix>
                <el-icon><Lock /></el-icon>
              </template>
            </el-input>
          </el-form-item>

          <el-form-item>
            <el-button
              type="primary"
              size="large"
              :loading="loading"
              @click="handleLogin"
              class="login-btn"
            >
              {{ loading ? 'Logging in...' : 'Login' }}
            </el-button>
          </el-form-item>
        </el-form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { ElMessage } from 'element-plus'
import { User, Lock } from '@element-plus/icons-vue'
import { login } from '@/api/admin/auth'

const router = useRouter()
const formRef = ref(null)
const loading = ref(false)

const formData = reactive({
  username: '',
  password: ''
})

const rules = {
  username: [
    { required: true, message: 'Please enter username', trigger: 'blur' }
  ],
  password: [
    { required: true, message: 'Please enter password', trigger: 'blur' }
  ]
}

const handleLogin = async () => {
  if (!formRef.value) return

  await formRef.value.validate(async (valid) => {
    if (valid) {
      loading.value = true
      try {
        const res = await login(formData)
        localStorage.setItem('admin_token', res.data.token)
        localStorage.setItem('admin_info', JSON.stringify(res.data.admin))
        ElMessage.success('Login successful!')
        router.push('/fanggangrong')
      } catch (error) {
        ElMessage.error('Login failed. Please check your credentials.')
      } finally {
        loading.value = false
      }
    }
  })
}
</script>

<style lang="scss" scoped>
.login-page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: $white;
}

.login-container {
  width: 100%;
  max-width: 450px;
  padding: 20px;
}

.login-box {
  background: $white;
  padding: 50px 40px;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);

  .login-form {
    :deep(.el-form-item__label) {
      font-weight: 500;
      color: #333;
    }

    .login-btn {
      width: 100%;
      margin-top: 10px;
      height: 45px;
      font-size: 16px;
      font-weight: 500;
    }
  }
}
</style>

