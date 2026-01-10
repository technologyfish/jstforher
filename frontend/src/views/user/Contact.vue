<template>
  <div class="contact-page">
    <div class="contact-container">
      <!-- Left Section - Form -->
      <div class="contact-form-section">
        <h2 class="contact-title">Work With Us</h2>

        <p class="form-intro mb-20">We're excited to partner with boutiques that share our passion for refined bridal elegance. Whether you have a quick question or are ready to request a catalog, drop us a message below.</p>
        <p class="form-intro font-weight-bold">Quick Terms:</p>
        <p class="form-intro term-item"><strong>MOQ：</strong> Starts from 2 pieces (mixed styles).</p>
        <p class="form-intro term-item"><strong>Lead Time：</strong> 7-15 business days.</p>
        <p class="form-intro term-item mb-20"><strong>Worldwide Shipping：</strong> DHL/FedEx/UPS Express.</p>

<!--        <p class="form-intro mb-20">-->
<!--          (Min Order: Starts from 2 pieces (mixed style). Production: 7-15 days.)-->
<!--        </p>-->
        <form class="contact-form" @submit.prevent="handleSubmit">
          <div class="form-row">
            <div class="form-group">
              <label for="firstName">First name *</label>
              <input 
                type="text" 
                id="firstName" 
                v-model="formData.firstName" 
                required 
              />
            </div>
            <div class="form-group">
              <label for="lastName">Last name *</label>
              <input 
                type="text" 
                id="lastName" 
                v-model="formData.lastName" 
                required 
              />
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email *</label>
            <input 
              type="email" 
              id="email" 
              v-model="formData.email" 
              required 
            />
          </div>

          <div class="form-group">
            <label for="businessInfo">Business Name / Website</label>
            <input 
              type="text" 
              id="businessInfo" 
              v-model="formData.businessInfo" 
              placeholder=""
            />
          </div>

          <div class="form-group">
            <label for="message">Write a message</label>
            <textarea 
              id="message" 
              v-model="formData.message" 
              rows="5"
            ></textarea>
          </div>

          <button type="submit" class="submit-btn" :disabled="loading">
            {{ loading ? 'Sending...' : 'Submit' }}
          </button>
        </form>
      </div>

      <!-- Right Section - Image -->
      <div class="contact-image-section">
        <img :src="contactImage" alt="Contact" />
      </div>
    </div>

    <!-- Info Section -->
<!--    <div class="info-section">-->
<!--      <h2 class="info-title">Refined Bridal Veils & Accessories</h2>-->
<!--      <p class="info-description">-->
<!--        JST FOR HER designs bridal veils with a focus on timeless elegance and modern simplicity. Each style is carefully considered to enhance the bride's overall look, offering balance, grace, and graceful movement. Our aesthetic values restraint, comfort, and versatility, allowing boutiques to present veils that feel elevated, confident, and beautifully understated for the wedding day and beyond.-->
<!--      </p>-->
<!--&lt;!&ndash;      <div class="moq-info">&ndash;&gt;-->
<!--&lt;!&ndash;        <h3 class="moq-title">Low MOQ & Lead Time</h3>&ndash;&gt;-->
<!--&lt;!&ndash;        <p class="moq-detail"><strong>Min Order:</strong> Starts from 2 pieces (mixed style).</p>&ndash;&gt;-->
<!--&lt;!&ndash;        <p class="moq-detail"><strong>Production:</strong> 7-15 days.</p>&ndash;&gt;-->
<!--&lt;!&ndash;      </div>&ndash;&gt;-->
<!--    </div>-->
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { ElMessage } from 'element-plus'
import { submitContactForm } from '@/api/contact'
import contactImage from '@/assets/images/img-8.png'

const loading = ref(false)

const formData = reactive({
  firstName: '',
  lastName: '',
  email: '',
  businessInfo: '',
  message: ''
})

const handleSubmit = async () => {
  loading.value = true
  try {
    // 合并firstName和lastName为name
    const submitData = {
      name: `${formData.firstName} ${formData.lastName}`.trim(),
      email: formData.email,
      business_info: formData.businessInfo,
      message: formData.message || ''
    }
    
    await submitContactForm(submitData)
    ElMessage.success('Message sent successfully!')
    
    // 重置表单
    formData.firstName = ''
    formData.lastName = ''
    formData.email = ''
    formData.businessInfo = ''
    formData.message = ''
  } catch (error) {
    const message = error.response?.data?.message || 'Failed to send message. Please try again.'
    ElMessage.error(message)
  } finally {
    loading.value = false
  }
}
</script>

<style lang="scss" scoped>
.contact-page {
  min-height: 100vh;
  padding-top: 70px;
  background: $white;
}

.contact-container {
  display: flex;
  min-height: 800px;
  align-items: stretch;
}

// Left Section - Form
.contact-form-section {
  flex: 0 0 50%;
  width: 50%;
  background: #FFECE5;
  padding: 80px 100px;
  display: flex;
  flex-direction: column;
  justify-content: center;

  .contact-title {
    font-size: 32px;
    font-weight: 400;
    color: #333;
    margin-bottom: 20px;
  }

  .contact-info-text {
    margin-bottom: 30px;

    p {
      font-size: 15px;
      line-height: 1.8;
      color: #666;
      margin: 0;
    }

    .email-link {
      display: inline-block;
      margin-top: 15px;
      font-size: 15px;
      color: #333;
      text-decoration: underline;
      transition: color 0.3s;

      &:hover {
        color: #666;
      }
    }
  }

  .divider {
    text-align: center;
    font-size: 20px;
    color: #999;
    margin: 30px 0;
  }

  .faq-text {
    font-size: 15px;
    line-height: 1.8;
    color: #666;
    margin-bottom: 30px;

    .link {
      color: #333;
      text-decoration: underline;
      transition: color 0.3s;

      &:hover {
        color: #666;
      }
    }
  }

  .form-intro {
    font-size: 15px;
    line-height: 2;
    color: #333;
    //margin-bottom: 10px;
  }

  .term-item {
    position: relative;
    padding-left: 10px;
    display: flex;
    align-items: center;

    &::before {
      content: '•';
      position: absolute;
      left: 0;
      color: #333;
      font-size: 18px;
      line-height: 1.8;
    }

    strong {
      font-weight: 600;
      color: #333;
    }
  }
  
  .mb-20{
    margin-bottom: 10px;
  }

  .contact-form {
    margin-top: 20px;
    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 25px;
    }

    .form-group {
      margin-bottom: 25px;

      label {
        display: block;
        font-size: 13px;
        color: #333;
        margin-bottom: 8px;
      }

      input,
      textarea {
        width: 100%;
        padding: 12px 15px;
        border: none;
        border-bottom: 1px solid #D0D0D0;
        background: transparent;
        font-size: 14px;
        color: #333;
        transition: border-color 0.3s;

        &:focus {
          outline: none;
          border-bottom-color: #999;
        }

        &::placeholder {
          color: #999;
        }
      }

      textarea {
        resize: vertical;
        min-height: 100px;
      }
    }

    .submit-btn {
      padding: 14px 50px;
      background: #8B7355;
      color: $white;
      border: none;
      cursor: pointer;
      font-size: 14px;
      font-weight: 400;
      transition: background 0.3s;
      margin-top: 20px;

      &:hover:not(:disabled) {
        background: #6F5A42;
      }

      &:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }
    }
  }
}

// Right Section - Image
.contact-image-section {
  flex: 0 0 50%;
  width: 50%;
  overflow: hidden;

  img {
    width: 100%;
    height: 100%;
    min-height: 800px;
    object-fit: cover;
    display: block;
  }
}

// Info Section
.info-section {
  max-width: 900px;
  margin: 0 auto;
  padding: 80px 40px;
  text-align: center;

  .info-title {
    font-size: 28px;
    font-weight: 400;
    color: #333;
    margin-bottom: 30px;
    line-height: 1.4;
  }

  .info-description {
    font-size: 15px;
    line-height: 1.8;
    color: #666;
    margin-bottom: 50px;
    text-align: justify;
    text-align-last: center;
  }

  .moq-info {
    padding: 10px 50px;
    border-radius: 8px;
    max-width: 600px;
    margin: 0 auto;

    .moq-title {
      font-size: 20px;
      font-weight: 500;
      color: #333;
      margin-bottom: 20px;
    }

    .moq-detail {
      font-size: 15px;
      line-height: 1.8;
      color: #666;
      margin: 10px 0;
      text-align: center;

      strong {
        color: #333;
        font-weight: 500;
      }
    }
  }
}

@media (max-width: $breakpoint-mobile) {
  .contact-page {
    padding-top: pxtovw(80);
  }

  .contact-container {
    flex-direction: column;
    min-height: auto;
  }

  .contact-form-section {
    width: 100%;
    padding: pxtovw(80) pxtovw(40);

    .contact-title {
      font-size: pxtovw(48);
      line-height: 1.3;
      margin-bottom: pxtovw(40);
    }

    .contact-info-text,
    .faq-text,
    .form-intro {
      font-size: pxtovw(28);
      line-height: 1.8;
      //margin-bottom: pxtovw(10);

      br {
        display: none;
      }
    }

    .term-item {
      padding-left: pxtovw(30);

      &::before {
        font-size: pxtovw(32);
      }
    }

    .mb-20{
      margin-bottom: pxtovw(20);
    }

    .contact-form {
      .form-row {
        grid-template-columns: 1fr;
        gap: 0;
      }

      .form-group {
        margin-bottom: pxtovw(30);
        
        label {
          font-size: pxtovw(24);
          margin-bottom: pxtovw(10);
        }
        
        input,
        textarea {
          padding: pxtovw(20) pxtovw(24);
          font-size: pxtovw(28);
          border-width: pxtovw(2);
        }
        
        textarea {
          min-height: pxtovw(200);
        }
      }

      .submit-btn {
        padding: pxtovw(20) pxtovw(60);
        font-size: pxtovw(24);
        margin-top: pxtovw(30);
      }
    }
  }

  .contact-image-section {
    width: 100%;
    height: pxtovw(600);

    img {
      min-height: pxtovw(600);
    }
  }

  .info-section {
    padding: pxtovw(100) pxtovw(40);

    .info-title {
      font-size: pxtovw(42);
      margin-bottom: pxtovw(40);
      line-height: 1.4;
    }

    .info-description {
      font-size: pxtovw(26);
      line-height: 1.8;
      margin-bottom: pxtovw(60);
    }

    .moq-info {
      padding: pxtovw(10) pxtovw(40);
      border-radius: pxtovw(12);

      .moq-title {
        font-size: pxtovw(32);
        margin-bottom: pxtovw(30);
      }

      .moq-detail {
        font-size: pxtovw(26);
        line-height: 1.8;
        margin: pxtovw(15) 0;
      }
    }
  }
}
.font-weight-bold{
  font-weight: bold;
}
</style>

