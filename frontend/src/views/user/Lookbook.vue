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

        <div v-else class="content-wrapper" :class="{ loading: loadingMore }">
          <!-- Pagination Loading Overlay -->
          <div v-if="loadingMore" class="pagination-loading-overlay">
            <div class="loading-spinner"></div>
            <p>Loading...</p>
          </div>

          <div class="sub-categories-grid">
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

          <!-- 分页器 -->
          <div class="pagination" v-if="pagination.last_page > 1">
            <button 
              class="page-btn prev"
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage === 1 || loadingMore"
            >
              ‹
            </button>
            
            <button
              v-for="page in displayPages"
              :key="page"
              class="page-btn"
              :class="{ active: page === currentPage, ellipsis: page === '...' }"
              @click="page !== '...' && goToPage(page)"
              :disabled="page === '...' || loadingMore"
            >
              {{ page }}
            </button>
            
            <button 
              class="page-btn next"
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage === pagination.last_page || loadingMore"
            >
              ›
            </button>
          </div>
        </div>
      </main>
    </div>

    <!-- Image Gallery Modal -->
    <teleport to="body">
      <div v-if="showGallery" class="gallery-modal" @click="closeGallery">
        <div class="gallery-content" @click.stop>
          <!-- Close Button -->
          <button class="close-btn" @click="closeGallery">
            <img src="@/assets/images/icon-close.png" alt="Close" />
          </button>

          <!-- Main Image -->
          <div class="main-image">
            <!-- Navigation Arrows -->
            <button class="nav-btn prev" @click="prevImage" v-if="currentImages.length > 1">
              <img src="@/assets/images/icon-prev.png" alt="Previous" />
            </button>
            <button class="nav-btn next" @click="nextImage" v-if="currentImages.length > 1">
              <img src="@/assets/images/icon-next.png" alt="Next" />
            </button>
            
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
            <button @click="zoomOut">
              <img src="@/assets/images/icon-jian.png" alt="Zoom Out" />
            </button>
            <span>{{ Math.round(zoomLevel * 100) }}%</span>
            <button @click="zoomIn">
              <img src="@/assets/images/icon-add.png" alt="Zoom In" />
            </button>
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
const loadingMore = ref(false)
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

// 分页相关
const currentPage = ref(1)
const perPage = 9
const pagination = ref({
  total: 0,
  current_page: 1,
  last_page: 1,
  has_more: false
})

const displayedSubCategories = computed(() => {
  if (selectedCategory.value === null) {
    // 选择 All，显示所有二级栏目
    return allSubCategories.value
  }
  // 选择特定栏目，显示该栏目下的二级栏目
  return subCategories.value
})

// 计算显示的页码
const displayPages = computed(() => {
  const pages = []
  const total = pagination.value.last_page
  const current = currentPage.value
  
  if (total <= 7) {
    // 总页数小于等于7，全部显示
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    // 总页数大于7，显示部分页码
    if (current <= 3) {
      // 当前页在前3页
      for (let i = 1; i <= 5; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(total)
    } else if (current >= total - 2) {
      // 当前页在后3页
      pages.push(1)
      pages.push('...')
      for (let i = total - 4; i <= total; i++) {
        pages.push(i)
      }
    } else {
      // 当前页在中间
      pages.push(1)
      pages.push('...')
      for (let i = current - 1; i <= current + 1; i++) {
        pages.push(i)
      }
      pages.push('...')
      pages.push(total)
    }
  }
  
  return pages
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
  // 重置分页
  currentPage.value = 1
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
    const res = await getSubCategories({ 
      category_id: categoryId,
      page: currentPage.value,
      per_page: perPage
    })
    
    subCategories.value = res.data || []
    
    // 更新分页信息
    if (res.pagination) {
      pagination.value = res.pagination
      hasMore.value = res.pagination.has_more
    } else {
      hasMore.value = false
    }
  } catch (error) {
    console.error('Failed to load sub-categories:', error)
  }
}

const loadAllSubCategories = async () => {
  try {
    const res = await getSubCategories({
      page: currentPage.value,
      per_page: perPage
    })
    
    allSubCategories.value = res.data || []
    
    // 更新分页信息
    if (res.pagination) {
      pagination.value = res.pagination
      hasMore.value = res.pagination.has_more
    } else {
      hasMore.value = false
    }
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

const goToPage = async (page) => {
  if (page < 1 || page > pagination.value.last_page || page === currentPage.value || loadingMore.value) {
    return
  }
  
  loadingMore.value = true
  currentPage.value = page
  
  try {
    // 重新加载数据
    if (selectedCategory.value === null) {
      await loadAllSubCategories()
    } else {
      await loadSubCategories(selectedCategory.value.id)
    }
    
    // 滚动到顶部
    window.scrollTo({ top: 0, behavior: 'smooth' })
  } finally {
    loadingMore.value = false
  }
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

  .content-wrapper {
    position: relative;
    min-height: 400px;
  }

  .pagination-loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    z-index: 10;
    backdrop-filter: blur(3px);
    transition: opacity 0.2s;

    .loading-spinner {
      width: 60px;
      height: 60px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid $primary-color;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    p {
      margin-top: 20px;
      font-size: 16px;
      color: #666;
      font-weight: 500;
    }
  }

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

  // 分页器样式
  .pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 50px;

    .page-btn {
      min-width: 40px;
      height: 40px;
      padding: 0 12px;
      border: 1px solid #e0e0e0;
      background: $white;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s;
      border-radius: 4px;
      position: relative;

      &:hover:not(:disabled):not(.ellipsis) {
        border-color: $primary-color;
        color: $primary-color;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      }

      &.active {
        background: $primary-color;
        color: $white;
        border-color: $primary-color;
        font-weight: 500;
      }

      &:disabled:not(.ellipsis) {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f5f5f5;
        pointer-events: none;
      }

      &.ellipsis {
        border: none;
        cursor: default;
        background: transparent;
        pointer-events: none;
      }

      &.prev,
      &.next {
        font-size: 20px;
        font-weight: 300;
      }
    }
  }

  // 分页器整体在加载时的样式
  .content-wrapper.loading {
    pointer-events: none;
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
  width: 90vw;
  max-width: 1200px;
  height: 80vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;

  .close-btn {
    position: fixed;
    top: 20px;
    right: 20px;
    width: 40px;
    height: 40px;
    background: rgba(0, 0, 0, 0.5);
    border: none;
    cursor: pointer;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s;
    z-index: 100;
    padding: 0;

    &:hover {
      background: rgba(0, 0, 0, 0.7);
    }

    img {
      width: 20px;
      height: 20px;
      object-fit: contain;
    }
  }

  .main-image {
    position: relative;
    width: 100%;
    max-width: 800px;
    height: calc(90vh - 180px);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;

    .nav-btn {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      width: 50px;
      height: 50px;
      background: rgba(0, 0, 0, 0.5);
      border: none;
      cursor: pointer;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.3s;
      z-index: 10;
      padding: 0;

      &:hover {
        background: rgba(0, 0, 0, 0.7);
      }

      &.prev {
        left: 10px;
      }

      &.next {
        right: 10px;
      }

      img {
        width: 24px;
        height: 24px;
        object-fit: contain;
        cursor: pointer;
      }
    }

    > img {
      max-width: 100%;
      max-height: 100%;
      width: auto;
      height: auto;
      object-fit: contain;
      transition: transform 0.3s ease;
      cursor: zoom-in;
    }
  }

  .thumbnails {
    display: flex;
    gap: 8px;
    max-width: 800px;
    padding: 10px 0;
    overflow-x: auto;
    
    // 隐藏滚动条但保持可滚动
    scrollbar-width: none; // Firefox
    -ms-overflow-style: none; // IE and Edge
    
    &::-webkit-scrollbar {
      display: none; // Chrome, Safari, Opera
    }

    .thumbnail {
      width: 60px;
      height: 60px;
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
    position: absolute;
    bottom: -60px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    align-items: center;
    gap: 15px;
    background: rgba(255, 255, 255, 0.1);
    padding: 10px 20px;
    border-radius: 25px;
    z-index: 10;

    button {
      width: 30px;
      height: 30px;
      background: transparent;
      border: 1px solid $white;
      border-radius: 50%;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background 0.3s;
      padding: 0;

      &:hover {
        background: rgba(255, 255, 255, 0.2);
      }

      img {
        width: 16px;
        height: 16px;
        object-fit: contain;
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
    .content-wrapper {
      min-height: 50vh;
    }

    .pagination-loading-overlay {
      .loading-spinner {
        width: pxtovw(80);
        height: pxtovw(80);
        border-width: pxtovw(8);
      }

      p {
        margin-top: pxtovw(30);
        font-size: pxtovw(28);
      }
    }

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
    width: 95vw;
    height: 80vh;

    .close-btn {
      top: pxtovw(20);
      right: pxtovw(20);
      width: pxtovw(60);
      height: pxtovw(60);
      font-size: pxtovw(40);
    }

    .main-image {
      width: 90vw;
      height: 70vh;
      margin-bottom: pxtovw(30);

      .nav-btn {
        width: pxtovw(60);
        height: pxtovw(60);
        font-size: pxtovw(36);

        &.prev {
          left: pxtovw(30);
        }

        &.next {
          right: pxtovw(30);
        }
      }
    }

    .thumbnails {
      max-width: 90vw;
      gap: pxtovw(12);
      padding: pxtovw(10) 0;
      
      .thumbnail {
        width: pxtovw(80);
        height: pxtovw(80);
        border-width: pxtovw(3);
      }
    }

    .zoom-controls {
      bottom: pxtovw(-80);
      padding: pxtovw(15) pxtovw(30);
      gap: pxtovw(20);

      button {
        width: pxtovw(40);
        height: pxtovw(40);
        font-size: pxtovw(24);
      }

      span {
        font-size: pxtovw(20);
        min-width: pxtovw(80);
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
  
  .pagination {
    margin-top: pxtovw(50);
    gap: pxtovw(8);

    .page-btn {
      min-width: pxtovw(60);
      height: pxtovw(60);
      padding: 0 pxtovw(15);
      font-size: pxtovw(24);
      border-radius: pxtovw(6);
      border-width: pxtovw(2);

      &.prev,
      &.next {
        font-size: pxtovw(32);
      }
    }
  }
}
</style>

