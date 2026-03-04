<template>
  <div class="contact-page">
    <div class="contact-container">
      <!-- Left Section - Form -->
      <div class="contact-form-section">

        <!-- Page Header -->
        <h2 class="contact-title">Start Your Wholesale Partnership</h2>
        <p class="contact-subtitle">
          Work directly with our studio to access refined bridal veils, low MOQs, and reliable service.
        </p>

        <hr class="section-divider" />

        <!-- Intro lines -->
        <p class="intro-italic">No minimum commitment — feel free to inquire.</p>
        <p class="intro-italic">Just tell us a bit about your boutique — we'll take care of the rest.</p>

        <!-- Form block -->
        <h3 class="form-heading">WHOLESALE INQUIRY FORM</h3>
        <hr class="section-divider" />

        <form class="contact-form" @submit.prevent="handleSubmit" novalidate>

          <div class="form-group">
            <label for="firstName">Name <span class="required">*</span></label>
            <input type="text" id="firstName" ref="firstNameRef" v-model="formData.firstName" @input="errors.firstName = formData.firstName.trim() ? '' : 'Please enter your name so we can get in touch.'" />
            <p v-if="errors.firstName" class="field-error">{{ errors.firstName }}</p>
          </div>

          <div class="form-group">
            <label for="email">Email <span class="required">*</span></label>
            <input type="email" id="email" ref="emailRef" v-model="formData.email" @input="errors.email = formData.email.trim() ? '' : 'Please enter a valid email address.'" />
            <p v-if="errors.email" class="field-error">{{ errors.email }}</p>
          </div>

          <div class="form-group">
            <label for="businessInfo">Business Name or Website <span class="optional">(optional)</span></label>
            <input type="text" id="businessInfo" v-model="formData.businessInfo" />
          </div>

          <div class="form-group">
            <label for="location">Your Location <span class="optional">(optional)</span></label>
            <input type="text" id="location" v-model="formData.location" />
          </div>

          <div class="form-group">
            <label for="estimatedQuantity">Estimated Order Quantity <span class="optional">(optional)</span></label>
            <input type="text" id="estimatedQuantity" v-model="formData.estimatedQuantity" />
          </div>

          <div class="form-group">
            <label for="message">Message <span class="optional">(optional)</span></label>
            <textarea
              id="message"
              v-model="formData.message"
              rows="4"
              placeholder="Tell us about your boutique or what you're looking for"
            ></textarea>
          </div>

          <button type="submit" class="submit-btn" :disabled="loading">
            {{ loading ? 'SENDING...' : 'REQUEST CATALOG' }}
          </button>

          <p class="response-note">We typically respond within 24 hours.</p>

          <!-- 成功提示 -->
          <transition name="fade-notice">
            <div v-if="submitSuccess" class="success-notice">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
              </svg>
              <div>
                <p>Submitted successfully!</p>
              </div>
              <button class="success-close" @click="submitSuccess = false">✕</button>
            </div>
          </transition>
        </form>

        <!-- What You Need to Know -->
        <hr class="section-divider" />
        <div class="info-block">
          <h4 class="info-heading">What You Need to Know</h4>
          <ul class="info-list">
            <li><strong>Low MOQ:</strong> Starts from 2 pieces (mixed styles)</li>
            <li><strong>Lead Time:</strong> 7–15 business days</li>
            <li><strong>Worldwide Shipping:</strong> DHL / FedEx / UPS Express</li>
          </ul>
        </div>

      </div>

      <!-- Right Section - Image -->
      <div class="contact-image-section">
        <img :src="contactImage" alt="Contact" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, nextTick } from 'vue'
import { ElMessage } from 'element-plus'
import { submitContactForm } from '@/api/contact'
import contactImage from '@/assets/images/img-8.png'

const loading = ref(false)
const submitSuccess = ref(false)
const firstNameRef = ref(null)
const emailRef = ref(null)

const formData = reactive({
  firstName: '',
  email: '',
  businessInfo: '',
  location: '',
  estimatedQuantity: '',
  message: ''
})

const errors = reactive({
  firstName: '',
  email: ''
})

const validate = async () => {
  errors.firstName = ''
  errors.email = ''
  let valid = true
  let firstErrorRef = null

  if (!formData.firstName.trim()) {
    errors.firstName = 'Please enter your name so we can get in touch.'
    valid = false
    firstErrorRef = firstNameRef
  }

  const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!formData.email.trim()) {
    errors.email = 'Please enter a valid email address.'
    if (!firstErrorRef) firstErrorRef = emailRef
    valid = false
  } else if (!emailReg.test(formData.email.trim())) {
    errors.email = 'Please enter a valid email address.'
    if (!firstErrorRef) firstErrorRef = emailRef
    valid = false
  }

  if (firstErrorRef) {
    await nextTick()
    firstErrorRef.value?.focus()
    firstErrorRef.value?.scrollIntoView({ behavior: 'smooth', block: 'center' })
  }

  return valid
}

const handleSubmit = async () => {
  if (!await validate()) return

  loading.value = true
  try {
    const submitData = {
      first_name:         formData.firstName,
      email:              formData.email,
      business_info:      formData.businessInfo,
      location:           formData.location,
      estimated_quantity: formData.estimatedQuantity,
      message:            formData.message || ''
    }

    await submitContactForm(submitData)

    // 重置表单
    Object.assign(formData, {
      firstName: '',
      email: '',
      businessInfo: '',
      location: '',
      estimatedQuantity: '',
      message: ''
    })
    errors.firstName = ''
    errors.email = ''
    submitSuccess.value = true
    setTimeout(() => { submitSuccess.value = false }, 6000)
  } catch (error) {
    ElMessage.error('Failed to send message. Please try again.')
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

// ─── Left Section ────────────────────────────────────────────────────────────
.contact-form-section {
  flex: 0 0 50%;
  width: 50%;
  background: #FFECE5;
  padding: 70px 80px 80px;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.contact-title {
  font-size: 30px;
  font-weight: 600;
  color: #222;
  margin-bottom: 12px;
  line-height: 1.3;
}

.contact-subtitle {
  font-size: 14px;
  line-height: 1.8;
  color: #555;
  margin-bottom: 0;
}

.section-divider {
  border: none;
  //border-top: 1px solid #ddd;
  margin: 15px 0;
}

.intro-italic {
  font-size: 13.5px;
  font-style: italic;
  color: #555;
  line-height: 1.8;
  margin: 0 0 4px;
}

.form-heading {
  font-size: 15px;
  font-weight: 700;
  color: #222;
  margin: 0 0 0;
  letter-spacing: 0.02em;
  margin-top: 20px;
}

// ─── Form ────────────────────────────────────────────────────────────────────
.contact-form {
  margin-top: 0;

  .form-group {
    margin-bottom: 18px;

    label {
      display: block;
      font-size: 13px;
      color: #333;
      margin-bottom: 6px;

      .required {
        color: #333;
      }

      .optional {
        font-size: 12px;
        color: #999;
        font-style: italic;
        font-weight: 400;
      }
    }

    input,
    textarea {
      width: 100%;
      padding: 8px 0;
      border: none;
      border-bottom: 1px solid #ccc;
      background: transparent;
      font-size: 14px;
      color: #333;
      transition: border-color 0.25s;
      box-sizing: border-box;

      &:focus {
        outline: none;
        border-bottom-color: #888;
      }

      &::placeholder {
        color: #bbb;
        font-style: italic;
      }
    }

    textarea {
      resize: vertical;
      min-height: 90px;
    }

    .field-error {
      margin: 5px 0 0;
      font-size: 12px;
      color: #c0392b;
      font-style: italic;
    }
  }

  .success-notice {
    position: fixed;
    top: 30%;
    left: 50%;
    transform: translateX(-50%);
    z-index: 9999;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 20px 24px;
    background: #f0faf4;
    border: 1px solid #6dbb8a;
    border-radius: 6px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    width: 90%;
    max-width: 420px;

    svg {
      flex-shrink: 0;
      width: 22px;
      height: 22px;
      color: #27ae60;
      margin-top: 2px;
    }

    div {
      flex: 1;
    }

    p {
      margin: 0;
      font-size: 14px;
      color: #1e7e44;
      line-height: 1.7;
      font-style: normal;
    }

    .success-close {
      flex-shrink: 0;
      background: none;
      border: none;
      cursor: pointer;
      font-size: 14px;
      color: #6dbb8a;
      padding: 0;
      line-height: 1;
      margin-top: 2px;
      &:hover { color: #27ae60; }
    }
  }

  .submit-btn {
    display: block;
    width: 100%;
    padding: 14px 20px;
    background: #4a4a4a;
    color: $white;
    border: none;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    transition: background 0.3s;
    margin-top: 10px;

    &:hover:not(:disabled) {
      background: #2e2e2e;
    }

    &:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }
  }

    .response-note {
      font-size: 12px;
      color: #999;
      text-align: center;
      margin-top: 10px;
      font-style: italic;
    }
  }

.fade-notice-enter-active {
  transition: opacity 0.4s, transform 0.4s;
}
.fade-notice-leave-active {
  transition: opacity 0.4s, transform 0.4s;
}
.fade-notice-enter-from {
  opacity: 0;
  transform: translateY(-8px);
}
.fade-notice-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

// ─── Info Block ──────────────────────────────────────────────────────────────
.info-block {
  .info-heading {
    font-size: 13.5px;
    font-weight: 700;
    color: #444;
    margin-bottom: 10px;
  }

  .info-list {
    list-style: disc;
    padding-left: 18px;
    margin: 0;

    li {
      font-size: 12.5px;
      color: #777;
      line-height: 1.9;
      font-style: italic;

      strong {
        font-style: normal;
        color: #555;
        font-weight: 600;
      }
    }
  }
}

// ─── Right Section ───────────────────────────────────────────────────────────
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

// ─── Mobile ──────────────────────────────────────────────────────────────────
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
  }

  .contact-title {
    font-size: pxtovw(52);
    margin-bottom: pxtovw(20);
  }

  .contact-subtitle {
    font-size: pxtovw(28);
  }

  .section-divider {
    margin: pxtovw(20) 0;
  }

  .intro-italic {
    font-size: pxtovw(26);
    margin-bottom: pxtovw(6);
  }

  .form-heading {
    font-size: pxtovw(30);
    margin-top: pxtovw(20)
  }

  .contact-form {
    .form-group {
      margin-bottom: pxtovw(30);

      label {
        font-size: pxtovw(24);
        margin-bottom: pxtovw(10);

        .optional {
          font-size: pxtovw(22);
        }
      }

      input,
      textarea {
        font-size: pxtovw(28);
        padding: pxtovw(12) 0;
      }

      textarea {
        min-height: pxtovw(180);
      }
    }

    .submit-btn {
      padding: pxtovw(28) pxtovw(20);
      font-size: pxtovw(24);
      letter-spacing: 0.1em;
      margin-top: pxtovw(20);
    }

    .response-note {
      font-size: pxtovw(22);
      margin-top: pxtovw(16);
    }
  }

  .info-block {
    .info-heading {
      font-size: pxtovw(28);
      margin-bottom: pxtovw(16);
    }

    .info-list {
      padding-left: pxtovw(30);

      li {
        font-size: pxtovw(24);
        line-height: 2;
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
}
</style>
