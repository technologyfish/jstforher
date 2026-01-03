<template>
  <footer class="footer">
    <div class="footer-container">
      <div class="footer-content">
        <!-- Left Section -->
        <div class="footer-left">
          <h3 class="brand-name">NEA • MILANO</h3>
          <p class="brand-description">
            Italy-based woman owned brand of accessories<br />
            and hair jewelry for brides and beyond.<br />
            We value quality, craftsmanship and<br />
            uniqueness. Our accessories are ethically made<br />
            and totally fab.
          </p>
          
          <!-- Links under brand info -->
          <div class="footer-links">
            <router-link to="/about">about</router-link>
            <router-link to="/contact">Contact</router-link>
          </div>
        </div>

        <!-- Right Section - Newsletter -->
        <div class="footer-right">
          <h4 class="newsletter-title">Enter Our Private List</h4>
          <form class="subscribe-form" @submit.prevent="handleSubscribe">
            <div class="input-group">
              <label for="email">Email *</label>
              <input 
                type="email" 
                id="email"
                v-model="email" 
                placeholder="" 
                required 
                :disabled="loading"
              />
            </div>
            <button type="submit" class="subscribe-btn" :disabled="loading">
              {{ loading ? 'Subscribing...' : 'Subscribe' }}
            </button>
            <label class="checkbox-label">
              <input type="checkbox" v-model="agreedToSubscribe" :disabled="loading" />
              <span>I want to subscribe to your mailing list.</span>
            </label>
          </form>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { ref } from 'vue'
import { ElMessage } from 'element-plus'
import { subscribeNewsletter } from '@/api/newsletter'

const email = ref('')
const agreedToSubscribe = ref(false)
const loading = ref(false)

const handleSubscribe = async () => {
  // 验证邮箱
  if (!email.value) {
    ElMessage.warning('Please enter your email address')
    return
  }

  // 验证邮箱格式
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(email.value)) {
    ElMessage.warning('Please enter a valid email address')
    return
  }

  // 验证是否同意订阅
  if (!agreedToSubscribe.value) {
    ElMessage.warning('Please agree to subscribe to our mailing list')
    return
  }

  loading.value = true
  try {
    await subscribeNewsletter({ email: email.value })
    ElMessage.success('Thank you for subscribing!')
    email.value = ''
    agreedToSubscribe.value = false
  } catch (error) {
    const message = error.response?.data?.message || 'Failed to subscribe. Please try again.'
    ElMessage.error(message)
  } finally {
    loading.value = false
  }
}
</script>

<style lang="scss" scoped>
.footer {
  background: #F5F5F5;
  padding: 80px 0 60px;
  border-top: 1px solid #E0E0E0;
}

.footer-container {
  max-width: 1440px;
  margin: 0 auto;
  padding: 0 80px;
}

.footer-content {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 100px;
}

// Left Section - Brand Info
.footer-left {
  flex: 1;
  max-width: 450px;

  .brand-name {
    font-size: 16px;
    font-weight: 600;
    letter-spacing: 2px;
    margin-bottom: 25px;
    color: #333;
  }

  .brand-description {
    font-size: 13px;
    line-height: 1.8;
    color: #666;
    margin: 0 0 30px 0;
  }

  .footer-links {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 30px;

    a {
      font-size: 13px;
      color: #333;
      text-decoration: underline;
      transition: color 0.3s;
      width: fit-content;

      &:hover {
        color: #666;
      }
    }
  }
}

// Right Section - Newsletter
.footer-right {
  flex: 1;
  max-width: 450px;

  .newsletter-title {
    font-size: 18px;
    font-weight: 400;
    margin-bottom: 25px;
    color: #333;
  }

  .subscribe-form {
    display: flex;
    flex-direction: column;
    gap: 15px;

    .input-group {
      display: flex;
      flex-direction: column;
      gap: 8px;

      label {
        font-size: 12px;
        color: #333;
      }

      input {
        padding: 12px 15px;
        border: 1px solid #D0D0D0;
        background: $white;
        font-size: 14px;
        transition: border-color 0.3s;

        &:focus {
          outline: none;
          border-color: #999;
        }

        &::placeholder {
          color: #999;
        }

        &:disabled {
          background: #F5F5F5;
          cursor: not-allowed;
        }
      }
    }

    .subscribe-btn {
      padding: 12px 40px;
      background: #333;
      color: $white;
      border: none;
      cursor: pointer;
      font-size: 13px;
      font-weight: 400;
      transition: background 0.3s;
      align-self: flex-start;
      text-transform: capitalize;

      &:hover:not(:disabled) {
        background: #000;
      }

      &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }
    }

    .checkbox-label {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      font-size: 12px;
      color: #666;
      cursor: pointer;
      margin-top: 5px;

      input[type="checkbox"] {
        margin-top: 2px;
        cursor: pointer;
        width: 16px;
        height: 16px;

        &:disabled {
          cursor: not-allowed;
        }
      }

      span {
        line-height: 1.5;
      }
    }
  }
}

@media (max-width: $breakpoint-mobile) {
  .footer {
    padding: pxtovw(80) 0 pxtovw(60);
  }

  .footer-container {
    padding: 0 pxtovw(30);
  }

  .footer-content {
    flex-direction: column;
    gap: pxtovw(60);
  }

  .footer-left,
  .footer-right {
    max-width: 100%;
  }

  .footer-left .brand-name {
    font-size: pxtovw(28);
    margin-bottom: pxtovw(30);
  }

  .footer-left .brand-description {
    font-size: pxtovw(24);
    line-height: 1.6;
    margin-bottom: pxtovw(40);
  }

  .footer-left .footer-links {
    gap: pxtovw(20);
    margin-top: pxtovw(40);
    
    a {
      font-size: pxtovw(24);
    }
  }

  .footer-right .newsletter-title {
    font-size: pxtovw(30);
    margin-bottom: pxtovw(30);
  }

  .footer-right .subscribe-form {
    gap: pxtovw(25);
    
    .input-group {
      gap: pxtovw(12);
      
      label {
        font-size: pxtovw(22);
      }
      
      input {
        padding: pxtovw(20) pxtovw(24);
        font-size: pxtovw(24);
        border-width: pxtovw(2);
      }
    }
    
    .subscribe-btn {
      padding: pxtovw(20) pxtovw(60);
      font-size: pxtovw(24);
    }
    
    .checkbox-label {
      gap: pxtovw(15);
      font-size: pxtovw(22);
      margin-top: pxtovw(10);
      
      input[type="checkbox"] {
        width: pxtovw(28);
        height: pxtovw(28);
        margin-top: pxtovw(4);
      }
    }
  }
}
</style>
