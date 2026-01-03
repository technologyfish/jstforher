<template>
  <div class="lookbook-page">
    <div class="page-header">
      <h1>Explore our products</h1>
    </div>

    <div class="lookbook-container">
      <!-- Left Sidebar - Categories -->
      <aside class="categories-sidebar">
        <h3 class="filter-title">Filter by</h3>
        <div class="category-section">
          <div class="category-header" @click="toggleCategory">
            <h4>Category</h4>
            <span class="toggle-icon">{{ categoryExpanded ? '−' : '+' }}</span>
          </div>
          <ul class="category-list" v-show="categoryExpanded">
            <li 
              :class="{ active: selectedCategory === null }"
              @click="selectCategory(null)"
            >
              All
            </li>
            <li 
              v-for="category in categories" 
              :key="category.id"
              :class="{ active: selectedCategory?.id === category.id }"
              @click="selectCategory(category)"
            >
              {{ category.name }}
            </li>
          </ul>
        </div>
      </aside>

      <!-- Right Content - Sub-categories Grid -->
      <main class="content-area">
        <!-- Loading State -->
        <div v-if="loading" class="loading-container">
          <div class="loading-spinner"></div>
          <p>Loading...</p>
        </div>

        <div v-else class="sub-categories-grid">
          <div 
            v-for="subCategory in displayedSubCategories" 
            :key="subCategory.id"
            class="sub-category-item"
            :class="{ 'no-images': !hasImages(subCategory) }"
            @click="hasImages(subCategory) ? openGallery(subCategory) : null"
          >
            <div class="sub-category-image">
              <img :src="subCategory.cover_image || 'https://via.placeholder.com/400x500'" :alt="subCategory.name" />
            </div>
            <h4 class="sub-category-name">{{ subCategory.name }}</h4>
          </div>
        </div>

        <div class="load-more" v-if="hasMore">
          <button @click="loadMore">Load More</button>
        </div>
      </main>
    </div>

    <!-- Image Gallery Modal -->
    <teleport to="body">
      <div v-if="showGallery" class="gallery-modal" @click="closeGallery">
        <div class="gallery-content" @click.stop>
          <!-- Close Button -->
          <button class="close-btn" @click="closeGallery">×</button>

          <!-- Title -->
          <h2 class="gallery-title">{{ currentSubCategory?.name }}</h2>

          <!-- Navigation Arrows -->
          <button class="nav-btn prev" @click="prevImage" v-if="currentImages.length > 1">
            ‹
          </button>
          <button class="nav-btn next" @click="nextImage" v-if="currentImages.length > 1">
            ›
          </button>

          <!-- Main Image -->
          <div class="main-image">
            <img 
              :src="currentImages[currentImageIndex]" 
              alt="Gallery Image"
              :style="{ transform: `scale(${zoomLevel})` }"
            />
          </div>

          <!-- Thumbnails -->
          <div class="thumbnails" v-if="currentImages.length > 1">
            <div 
              v-for="(image, index) in currentImages" 
              :key="index"
              class="thumbnail"
              :class="{ active: index === currentImageIndex }"
              @click="currentImageIndex = index"
            >
              <img :src="image" alt="" />
            </div>
          </div>

          <!-- Zoom Controls -->
          <div class="zoom-controls">
            <button @click="zoomOut">-</button>
            <span>{{ Math.round(zoomLevel * 100) }}%</span>
            <button @click="zoomIn">+</button>
          </div>
        </div>
      </div>
    </teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { getCategories, getSubCategories } from '@/api/category'
import { getArticles } from '@/api/article'

const loading = ref(false)
const categories = ref([])
const selectedCategory = ref(null)
const subCategories = ref([])
const allSubCategories = ref([])
const categoryExpanded = ref(true)
const showGallery = ref(false)
const currentSubCategory = ref(null)
const currentImageIndex = ref(0)
const zoomLevel = ref(1)
const hasMore = ref(false)

const displayedSubCategories = computed(() => {
  if (selectedCategory.value === null) {
    // 选择 All，显示所有二级栏目
    return allSubCategories.value
  }
  // 选择特定栏目，显示该栏目下的二级栏目
  return subCategories.value
})

const currentImages = computed(() => {
  if (!currentSubCategory.value) return []
  
  // 获取该二级栏目下的所有文章图片
  const articles = currentSubCategory.value.articles || []
  const images = []
  
  articles.forEach(article => {
    if (article.images && Array.isArray(article.images)) {
      images.push(...article.images)
    }
  })
  
  return images.length > 0 ? images : ['https://via.placeholder.com/800x1000']
})

const selectCategory = async (category) => {
  selectedCategory.value = category
  if (category === null) {
    // 选择 All，加载所有二级栏目
    await loadAllSubCategories()
  } else {
    await loadSubCategories(category.id)
  }
}

const toggleCategory = () => {
  categoryExpanded.value = !categoryExpanded.value
}

const loadCategories = async () => {
  loading.value = true
  try {
    const res = await getCategories()
    categories.value = res.data || []
    
    // 默认选择 All
    await selectCategory(null)
  } catch (error) {
    console.error('Failed to load categories:', error)
  } finally {
    loading.value = false
  }
}

const loadSubCategories = async (categoryId) => {
  try {
    const res = await getSubCategories({ category_id: categoryId })
    subCategories.value = res.data || []
  } catch (error) {
    console.error('Failed to load sub-categories:', error)
  }
}

const loadAllSubCategories = async () => {
  try {
    const res = await getSubCategories()
    allSubCategories.value = res.data || []
  } catch (error) {
    console.error('Failed to load all sub-categories:', error)
  }
}

const openGallery = async (subCategory) => {
  // 检查是否有图片集
  if (!subCategory.articles || subCategory.articles.length === 0) {
    return
  }
  
  currentSubCategory.value = subCategory
  currentImageIndex.value = 0
  zoomLevel.value = 1
  showGallery.value = true
  document.body.style.overflow = 'hidden'
}

const closeGallery = () => {
  showGallery.value = false
  document.body.style.overflow = ''
}

const prevImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
  } else {
    currentImageIndex.value = currentImages.value.length - 1
  }
}

const nextImage = () => {
  if (currentImageIndex.value < currentImages.value.length - 1) {
    currentImageIndex.value++
  } else {
    currentImageIndex.value = 0
  }
}

const zoomIn = () => {
  if (zoomLevel.value < 2) {
    zoomLevel.value += 0.25
  }
}

const zoomOut = () => {
  if (zoomLevel.value > 0.75) {
    zoomLevel.value -= 0.25
  }
}

const loadMore = () => {
  // TODO: 实现加载更多
}

// 检查产品册是否有图片集
const hasImages = (subCategory) => {
  return subCategory.articles && subCategory.articles.length > 0
}

onMounted(() => {
  loadCategories()
})
</script>

<style lang="scss" scoped>
.lookbook-page {
  min-height: 100vh;
  padding-top: 80px;
}

.page-header {
  text-align: center;
  padding: 60px 0;
  background: #fafafa;

  h1 {
    font-size: 28px;
    font-weight: 400;
  }
}

.lookbook-container {
  display: flex;
  gap: 40px;
  padding: 60px;
  max-width: 1440px;
  margin: 0 auto;
}

// Loading
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 400px;
  
  .loading-spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #000;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  p {
    margin-top: 20px;
    font-size: 16px;
    color: #666;
  }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

// Sidebar
.categories-sidebar {
  width: 250px;
  flex-shrink: 0;

  .filter-title {
    font-size: 18px;
    font-weight: 500;
    margin: 0 0 30px 0;
    padding-bottom: 15px;
    border-bottom: 1px solid #e0e0e0;
    text-transform: capitalize;
  }

  .category-section {
    .category-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      cursor: pointer;
      margin-bottom: 15px;
      padding-bottom: 10px;
      h4 {
        font-size: 16px;
        margin: 0;
        font-weight: 500;
      }

      .toggle-icon {
        font-size: 20px;
        font-weight: 300;
        user-select: none;
      }

      &:hover {
        .toggle-icon {
          color: $primary-color;
        }
      }
    }

    .category-list {
      list-style: none;

      li {
        padding: 10px 0;
        cursor: pointer;
        font-size: 14px;
        transition: color 0.3s;

        &:hover,
        &.active {
          color: $primary-color;
          font-weight: 500;
        }
      }
    }
  }
}

// Content Area
.content-area {
  flex: 1;

  .sub-categories-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
  }

  .sub-category-item {
    cursor: pointer;
    transition: transform 0.3s;

    &:hover {
      transform: translateY(-5px);
    }

    .sub-category-image {
      overflow: hidden;
      margin-bottom: 15px;
      border-radius: 4px;

      img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        transition: transform 0.3s;
      }
    }

    &:hover .sub-category-image img {
      transform: scale(1.05);
    }

    .sub-category-name {
      text-align: center;
      font-size: 16px;
      font-weight: 400;
    }

    // 没有图片集的产品册样式
    &.no-images {
      cursor: default;
      //opacity: 0.6;

      &:hover {
        transform: none;
      }

      &:hover .sub-category-image img {
        transform: none;
      }
    }
  }

  .load-more {
    text-align: center;
    margin-top: 50px;

    button {
      padding: 12px 50px;
      border: 2px solid $primary-color;
      background: transparent;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s;

      &:hover {
        background: $primary-color;
        color: $white;
      }
    }
  }
}

// Gallery Modal
.gallery-modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.95);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px;
}

.gallery-content {
  position: relative;
  max-width: 90vw;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  align-items: center;

  .close-btn {
    position: absolute;
    top: 0;
    right: -50px;
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    color: $white;
    border: none;
    font-size: 36px;
    cursor: pointer;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s;

    &:hover {
      background: rgba(255, 255, 255, 0.2);
    }
  }

  .gallery-title {
    position: absolute;
    top: -60px;
    left: 0;
    color: $white;
    font-size: 24px;
    font-weight: 400;
  }

  .nav-btn {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.1);
    color: $white;
    border: none;
    font-size: 36px;
    cursor: pointer;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s;

    &:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    &.prev {
      left: -80px;
    }

    &.next {
      right: -80px;
    }
  }

  .main-image {
    max-width: 600px;
    max-height: 600px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 40px 0;
    overflow: hidden;

    img {
      max-width: 100%;
      max-height: 100%;
      object-fit: contain;
      transition: transform 0.3s ease;
      cursor: zoom-in;
    }
  }

  .thumbnails {
    display: flex;
    gap: 10px;
    margin-top: 20px;
    overflow-x: auto;
    max-width: 600px;

    .thumbnail {
      width: 80px;
      height: 80px;
      flex-shrink: 0;
      cursor: pointer;
      opacity: 0.5;
      transition: opacity 0.3s;
      border: 2px solid transparent;
      border-radius: 4px;

      &:hover,
      &.active {
        opacity: 1;
        border-color: $white;
      }

      img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
      }
    }
  }

  .zoom-controls {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-top: 20px;
    background: rgba(255, 255, 255, 0.1);
    padding: 10px 20px;
    border-radius: 25px;

    button {
      width: 30px;
      height: 30px;
      background: transparent;
      color: $white;
      border: 1px solid $white;
      border-radius: 50%;
      font-size: 18px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.3s;

      &:hover {
        background: rgba(255, 255, 255, 0.2);
      }
    }

    span {
      color: $white;
      font-size: 14px;
      min-width: 50px;
      text-align: center;
    }
  }
}

@media (max-width: $breakpoint-mobile) {
  .lookbook-page {
    padding-top: pxtovw(80);
  }

  .page-header {
    padding: pxtovw(60) 0;

    h1 {
      font-size: pxtovw(48);
    }
  }

  .lookbook-container {
    flex-direction: column;
    padding: pxtovw(40) pxtovw(30);
    gap: pxtovw(40);
  }

  .categories-sidebar {
    width: 100%;
    
    .filter-title {
      font-size: pxtovw(32);
      margin-bottom: pxtovw(30);
    }
    
    .category-section {
      .category-header {
        padding: pxtovw(20) 0;
        
        h4 {
          font-size: pxtovw(28);
        }
        
        .toggle-icon {
          font-size: pxtovw(32);
        }
      }
      
      .category-list li {
        padding: pxtovw(15) 0;
        font-size: pxtovw(26);
      }
    }
  }

  .content-area {
    .sub-categories-grid {
      grid-template-columns: repeat(2, 1fr) !important;
      gap: pxtovw(30);
    }

    .sub-category-item {
      .sub-category-image img {
        height: pxtovw(450);
      }
      
      .sub-category-name {
        font-size: pxtovw(26);
        margin-top: pxtovw(15);
      }
    }
  }

  .gallery-modal {
    padding: pxtovw(30);
  }

  .gallery-content {
    .close-btn {
      top: pxtovw(-70);
      right: pxtovw(-15);
      width: pxtovw(60);
      height: pxtovw(60);
      font-size: pxtovw(40);
    }

    .gallery-title {
      top: pxtovw(-70);
      font-size: pxtovw(30);
    }

    .main-image-container {
      height: pxtovw(800);
      
      img {
        max-height: pxtovw(800);
      }
    }

    .nav-btn {
      width: pxtovw(60);
      height: pxtovw(60);
      font-size: pxtovw(36);

      &.prev {
        left: pxtovw(-70);
      }

      &.next {
        right: pxtovw(-70);
      }
    }

    .thumbnails {
      margin-top: pxtovw(30);
      gap: pxtovw(15);
      
      .thumbnail {
        width: pxtovw(100);
        height: pxtovw(100);
        border-width: pxtovw(3);
      }
    }
  }
  
  .loading-container {
    min-height: 50vh;
    padding: pxtovw(150) pxtovw(30);
    
    p {
      margin-top: pxtovw(30);
      font-size: pxtovw(28);
    }
  }
  
  .loading-spinner {
    width: pxtovw(70);
    height: pxtovw(70);
    border-width: pxtovw(6);
  }
}
</style>

