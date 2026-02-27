<template>
  <div class="product-detail-page">

    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
      <nav class="breadcrumb">
        <router-link to="/">Home</router-link>
        <span class="sep">/</span>
        <router-link to="/lookbook">{{ categoryName }}</router-link>
        <span class="sep">/</span>
        <span class="current">{{ product.name }}</span>
      </nav>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Loading...</p>
    </div>

    <!-- Main Content -->
    <div v-else class="detail-container">

      <!-- Left Block: Thumbnails + Main Image -->
      <div class="left-block">

        <!-- Thumbnail List -->
        <div class="thumb-panel">
          <div
            v-for="(img, index) in allImages"
            :key="index"
            class="thumb-item"
            :class="{ active: currentIndex === index }"
            @click="selectImage(index)"
          >
            <img :src="img" :alt="`Image ${index + 1}`" />
          </div>
        </div>

        <!-- Main Image -->
        <div class="main-image-panel">
          <div class="main-image-wrap" @click="openLightbox(currentIndex)">
            <img
              v-if="allImages.length > 0"
              :src="allImages[currentIndex]"
              :alt="product.name"
              class="main-img"
            />
            <div class="zoom-hint">
              <span>&#x2315;</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Info Panel -->
      <div class="info-panel">
        <!-- Product Name -->
        <h1 class="product-name">{{ product.name }}</h1>

        <hr class="divider" />

        <!-- Rich Text Description -->
        <div
          v-if="productDescription"
          class="product-description"
          v-html="productDescription"
        ></div>

        <hr class="divider" />

        <!-- CTA -->
        <div class="cta-block">
          <p class="cta-text">Ready to explore our collection?</p>
          <p class="cta-sub">Request your wholesale catalog today!</p>
          <router-link to="/contact" class="cta-btn">REQUEST CATALOG</router-link>
          <p class="cta-note">We typically respond within 24 hours.</p>
        </div>
      </div>
    </div>

    <!-- Lightbox -->
    <vue-easy-lightbox
      :visible="showLightbox"
      :imgs="allImages"
      :index="lightboxIndex"
      @hide="showLightbox = false"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import VueEasyLightbox from 'vue-easy-lightbox'
import { getSubCategory } from '@/api/category'

const route = useRoute()
const router = useRouter()

const loading = ref(true)
const product = ref({ name: '', description: '', category: null, articles: [] })
const currentIndex = ref(0)
const showLightbox = ref(false)
const lightboxIndex = ref(0)

const categoryName = computed(() => product.value.category?.name || 'Lookbook')

// 优先取第一个图片集的 description，fallback 到产品册自身的 description
const productDescription = computed(() => {
  const articles = product.value.articles || []
  for (const article of articles) {
    if (article.description) return article.description
  }
  return product.value.description || ''
})

const allImages = computed(() => {
  const articles = product.value.articles || []
  const imgs = []
  articles.forEach(article => {
    if (article.images && Array.isArray(article.images)) {
      imgs.push(...article.images)
    }
  })
  return imgs
})

const selectImage = (index) => {
  currentIndex.value = index
}

const openLightbox = (index) => {
  lightboxIndex.value = index
  showLightbox.value = true
}

// 打开 lightbox 后自动触发一次 zoom-in
watch(showLightbox, (val) => {
  if (val) {
    nextTick(() => {
      setTimeout(() => {
        // toolbar-btn 顺序：zoom-in(0), zoom-out(1), resize(2), rotate-left(3), rotate-right(4)
        const btns = document.querySelectorAll('.vel-toolbar .toolbar-btn')
        if (btns && btns[0]) {
          btns[0].click() // zoom-in 一次
        }
      }, 150)
    })
  }
})

onMounted(async () => {
  try {
    const res = await getSubCategory(route.params.id)
    product.value = res.data || {}
  } catch (e) {
    console.error('Failed to load product detail:', e)
  } finally {
    loading.value = false
  }
})
</script>

/* 全局覆盖 vue-easy-lightbox 默认图片尺寸，从 80% 放大到 92% */
<style lang="scss">
.vel-img {
  max-height: 92vh !important;
  max-width: 92vw !important;
}
</style>

<style lang="scss" scoped>
.product-detail-page {
  min-height: 100vh;
  padding-top: 70px;
  background: $white;
}

// ─── Breadcrumb ───────────────────────────────────────────────────────────────
.breadcrumb-bar {
  padding: 18px 60px;
  border-bottom: 1px solid #eee;
  background: #fafafa;
}

.breadcrumb {
  font-size: 13px;
  color: #999;

  a {
    color: #999;
    text-decoration: none;
    transition: color 0.2s;

    &:hover {
      color: #333;
    }
  }

  .sep {
    margin: 0 8px;
  }

  .current {
    color: #333;
    font-weight: 500;
  }
}

// ─── Loading ─────────────────────────────────────────────────────────────────
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 60vh;

  .loading-spinner {
    width: 48px;
    height: 48px;
    border: 3px solid #f0f0f0;
    border-top: 3px solid #333;
    border-radius: 50%;
    animation: spin 0.9s linear infinite;
  }

  p {
    margin-top: 16px;
    font-size: 14px;
    color: #999;
  }
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

// ─── Detail Layout ────────────────────────────────────────────────────────────
.detail-container {
  display: flex;
  align-items: flex-start;
  width: 100%;
  padding: 20px 60px;
  box-sizing: border-box;
  gap: 40px;
}

// ─── Left Block (Thumbnails + Main Image) ─────────────────────────────────────
.left-block {
  flex: 6;
  min-width: 0;
  display: flex;
  gap: 16px;
  align-items: flex-start;
}

// ─── Thumbnails ───────────────────────────────────────────────────────────────
.thumb-panel {
  width: 110px;
  flex-shrink: 0;
  display: flex;
  flex-direction: column;
  gap: 10px;
  max-height: 700px;
  overflow-y: auto;

  &::-webkit-scrollbar {
    width: 3px;
  }
  &::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 2px;
  }
}

.thumb-item {
  width: 110px;
  height: 138px;
  flex-shrink: 0;
  cursor: pointer;
  border: 2px solid transparent;
  overflow: hidden;
  transition: border-color 0.2s;

  img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.3s;
  }

  &:hover img {
    transform: scale(1.05);
  }

  &.active {
    border-color: #333;
  }
}

// ─── Main Image ───────────────────────────────────────────────────────────────
.main-image-panel {
  flex: 1;
  min-width: 0;
}

.main-image-wrap {
  position: relative;
  cursor: zoom-in;
  overflow: hidden;
  background: #f7f7f7;

  .main-img {
    width: 100%;
    height: auto;
    object-fit: cover;
    display: block;
    transition: transform 0.4s;
  }

  &:hover .main-img {
    transform: scale(1.03);
  }

  .zoom-hint {
    position: absolute;
    bottom: 16px;
    right: 16px;
    width: 36px;
    height: 36px;
    background: rgba(255,255,255,0.85);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #555;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.3s;
  }

  &:hover .zoom-hint {
    opacity: 1;
  }
}

// ─── Right Info ───────────────────────────────────────────────────────────────
.info-panel {
  flex: 4;
  min-width: 0;
  padding-left: 10px;
}

.product-name {
  font-size: 24px;
  font-weight: 500;
  color: #222;
  line-height: 1.3;
  margin: 0 0 20px;
  letter-spacing: 0.02em;
  text-transform: uppercase;
}

.divider {
  border: none;
  //border-top: 1px solid #e8e8e8;
  margin: 20px 0;
}

// Rich text styles
.product-description {
  font-size: 14px;
  line-height: 2;
  color: #555;

  :deep(p) {
    margin: 0 0 8px;
  }

  :deep(strong) {
    color: #333;
    font-weight: 600;
  }

  :deep(ul), :deep(ol) {
    padding-left: 20px;
    margin: 8px 0;
  }

  :deep(li) {
    margin-bottom: 4px;
  }

  :deep(a) {
    color: #333;
    text-decoration: underline;
  }
}

// ─── CTA Block ────────────────────────────────────────────────────────────────
.cta-block {
  text-align: left;
}

.cta-text {
  font-size: 15px;
  color: #333;
  font-weight: 500;
  margin: 0 0 4px;
}

.cta-sub {
  font-size: 13px;
  color: #666;
  margin: 0 0 16px;
}

.cta-btn {
  display: inline-block;
  padding: 13px 32px;
  background: #333;
  color: $white;
  text-decoration: none;
  font-size: 13px;
  font-weight: 600;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  transition: background 0.3s;

  &:hover {
    background: #111;
  }
}

.cta-note {
  font-size: 12px;
  color: #111;
  font-style: italic;
  margin: 12px 0 0;
}

// ─── Mobile ──────────────────────────────────────────────────────────────────
@media (max-width: $breakpoint-mobile) {
  .breadcrumb-bar {
    padding: pxtovw(24) pxtovw(30);
  }

  .breadcrumb {
    font-size: pxtovw(22);
  }

  .detail-container {
    flex-direction: column;
    padding: pxtovw(30) pxtovw(30);
    gap: pxtovw(30);
  }

  .left-block {
    flex: none;
    width: 100%;
    flex-direction: column;
  }

  .thumb-panel {
    width: 100%;
    flex-direction: row;
    max-height: none;
    overflow-x: auto;
    overflow-y: hidden;
    gap: pxtovw(12);
  }

  .thumb-item {
    width: pxtovw(130);
    height: pxtovw(160);
    flex-shrink: 0;
  }

  .main-image-panel {
    flex: none;
    width: 100%;
  }

  .main-image-wrap .main-img {
    height: pxtovw(700);
  }

  .info-panel {
    padding-left: 0;
    flex: none;
    width: 100%;
  }

  .product-name {
    font-size: pxtovw(40);
  }

  .product-description {
    font-size: pxtovw(26);
  }

  .cta-text {
    font-size: pxtovw(28);
  }

  .cta-sub {
    font-size: pxtovw(24);
  }

  .cta-btn {
    font-size: pxtovw(24);
    padding: pxtovw(24) pxtovw(50);
  }

  .cta-note {
    font-size: pxtovw(22);
  }
}
</style>
