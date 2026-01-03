import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src')
    }
  },
  server: {
    port: 3000,
    proxy: {
      // 只代理 /api 和 /admin/login、/admin/logout 等后端API
      '/api': {
        target: 'http://localhost:8000',
        changeOrigin: true
      }
    }
  },
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `
          @import "@/assets/scss/base/_variables.scss";
          @import "@/assets/scss/base/_function.scss";
        `
      }
    }
  }
})
