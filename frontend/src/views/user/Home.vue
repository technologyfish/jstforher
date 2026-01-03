<template>
  <div class="home-page">
    <!-- Loading State -->
    <div v-if="loading" class="loading-container">
      <div class="loading-spinner"></div>
      <p>Loading...</p>
    </div>

    <!-- Main Content -->
    <div v-else>
    <!-- Hero Section - Left Text Right Image -->
    <section class="hero-section">
      <div class="hero-content">
        <div class="hero-text">
          <h1 class="hero-title">Refined Bridal Veils <br />for Boutique Partners</h1>
          <p class="hero-description">
            Timeless elegance and curated collections<br />
            for independent bridal boutiques.
          </p>
        </div>
        <div class="hero-image">
          <img :src="heroImage" alt="Bridal Veil" />
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section class="about-section">
      <h2 class="section-title">Refined Bridal Veils & Accessories</h2>
      <p class="section-description">
        JST FOR HER designs bridal veils with a focus on timeless elegance and modern simplicity. Each style is carefully considered to enhance the bride’s overall look, offering softness, balance, and graceful movement. Our aesthetic values restraint, comfort, and versatility, allowing boutiques to present veils that feel elevated, confident, and beautifully understated for the wedding day and beyond.
      </p>
    </section>

    <!-- Collections Section with Swiper -->
    <section class="collections-section">
      <h3 class="collections-title">Shop by collections</h3>
      <div class="swiper-container">
        <swiper
          :modules="modules"
          :slides-per-view="4"
          :space-between="30"
          :navigation="{
            prevEl: '.collections-section .swiper-button-prev',
            nextEl: '.collections-section .swiper-button-next',
          }"
          :breakpoints="{
            320: { slidesPerView: 2, spaceBetween: 15 },
            768: { slidesPerView: 3, spaceBetween: 20 },
            1024: { slidesPerView: 4, spaceBetween: 30 },
          }"
          class="collections-swiper"
        >
          <swiper-slide 
            v-for="collection in collections" 
            :key="collection.id"
          >
            <div class="collection-item" @click="goToCollection(collection)">
              <div class="collection-image">
                <img :src="collection.image" :alt="collection.name" />
              </div>
              <h4 class="collection-name">{{ collection.name }}</h4>
            </div>
          </swiper-slide>
        </swiper>
        <div class="swiper-button-prev">
          <svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
            <path d="M742.758 87.398L706.56 51.2 256 501.76l450.56 450.56 36.198-36.198L328.397 501.76z" fill="#ffffff"></path>
          </svg>
        </div>
        <div class="swiper-button-next">
          <svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg">
            <path d="M742.758 87.398L706.56 51.2 256 501.76l450.56 450.56 36.198-36.198L328.397 501.76z" fill="#ffffff"></path>
          </svg>
        </div>
      </div>
    </section>

    <!-- Fashion Section - Left Image Right Text -->
    <section class="fashion-section">
      <div class="fashion-image">
        <img :src="fashionImage" alt="Responsible Fashion" />
      </div>
      <div class="fashion-content">
        <h3>A Brand Built for Boutiques</h3>
        <p>
          Jstfoher is built around long-term wholesale partnerships. We focus on creating bridal veil collections that are refined, dependable, and commercially considered. By offering curated selections, flexible MOQ, and clear communication, we support boutiques in presenting veils that align with their aesthetic and their clients’ expectations.
        </p>
        <h3>Want to learn more about our brand?</h3>
        <button class="read-more-btn" @click="goToAbout">Read About Us</button>
      </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="why-choose-section">
      <h3 class="section-title">Why Choose Us</h3>
      <div class="features-grid">
        <div 
          v-for="feature in features" 
          :key="feature.id"
          class="feature-item"
        >
          <div class="feature-image">
            <img :src="feature.image" :alt="feature.title" />
          </div>
          <h4 class="feature-title">{{ feature.title }}</h4>
          <p class="feature-description">{{ feature.description }}</p>
        </div>
      </div>
    </section>

    <!-- How to Working Us Section -->
    <section class="working-section">
      <div class="working-content">
        <h4 class="working-subtitle">How to Work With Us</h4>
        <div class="working-steps">
          <div class="step-item">
            <span class="step-number">1</span>
            <p class="step-text">Submit your wholesale inquiry and tell us about your boutique.</p>
          </div>
          <div class="step-item">
            <span class="step-number">2</span>
            <p class="step-text">Receive curated selections, pricing, and detailed information.</p>
          </div>
          <div class="step-item">
            <span class="step-number">3</span>
            <p class="step-text">Place your order and build a long-term partnership with us.</p>
          </div>
        </div>
      </div>
      <div class="working-image">
        <img :src="workingImage" alt="How to Work With Us" />
      </div>
    </section>
    </div>
    <!-- End Main Content -->

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { Swiper, SwiperSlide } from 'swiper/vue'
import { Navigation } from 'swiper/modules'
import 'swiper/css'
import 'swiper/css/navigation'
import { getProductList } from '@/api/product'

// Import images
import heroImage from '@/assets/images/img-1.jpg'
import fashionImage from '@/assets/images/img-2.png'
import workingImage from '@/assets/images/img-2.png'
import defaultCollectionImage from '@/assets/images/img-3.jpg'
import featureImage from '@/assets/images/img-5.jpg'

const router = useRouter()
const modules = [Navigation]

// Loading state
const loading = ref(true)

// 使用接口数据
const collections = ref([])

// 加载产品数据
const loadProducts = async () => {
  loading.value = true
  try {
    const res = await getProductList()
    // 将产品数据映射为 collections 格式
    collections.value = res.data.map(product => ({
      id: product.id,
      name: product.name,
      image: product.cover_image || defaultCollectionImage
    }))
  } catch (error) {
    console.error('Failed to load products:', error)
    // 如果加载失败，保持空数组
  } finally {
    loading.value = false
  }
}

const bestsellers = ref([

])

const features = ref([
  {
    id: 1,
    title: 'Boutique Focused',
    description: 'Designed exclusively for independent bridal boutiques and specialty retailers.',
    image: featureImage
  },
  {
    id: 2,
    title: 'Timeless Aesthetic',
    description: 'Refined designs created to remain relevant beyond seasonal trends.',
    image: featureImage
  },
  {
    id: 3,
    title: 'Reliable Production',
    description: 'Consistent quality control and dependable lead times you can plan around.',
    image: featureImage
  },
  {
    id: 4,
    title: 'Flexible MOQ',
    description: 'Wholesale terms that support growing and established boutiques alike.',
    image: featureImage
  }
])

const goToCollection = (collection) => {
  router.push('/lookbook')
}

const goToAbout = () => {
  router.push('/about')
}

onMounted(() => {
  loadProducts()
})
</script>

<style lang="scss" scoped>
.home-page {
  padding-top: 70px;
}

// Loading State
.loading-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 60vh;
  padding: 100px 20px;

  p {
    margin-top: 20px;
    font-size: 16px;
    color: #666;
  }
}

.loading-spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #333;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

// Hero Section - Left Text Right Image
.hero-section {
  width: 100%;
  background: #DEDDE2;
  padding: 0;

  .hero-content {
    display: flex;
    align-items: center;
    max-width: 100%;
    margin: 0 auto;
    min-height: 600px;
  }

  .hero-text {
    flex: 0 0 50%;
    width: 50%;
    padding: 80px 100px;

    .hero-title {
      font-size: 48px;
      line-height: 1.3;
      font-weight: 400;
      color: #333;
      margin-bottom: 30px;
      font-family: 'Georgia', serif;
    }

    .hero-description {
      font-size: 16px;
      line-height: 1.8;
      color: #666;
    }
  }

  .hero-image {
    flex: 0 0 50%;
    width: 50%;
    height: 600px;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }
  }
}

// About Section
.about-section {
  text-align: center;
  padding: 100px 40px;
  background: #FAFAFA;

  .section-title {
    font-size: 32px;
    margin-bottom: 35px;
    font-weight: 400;
    color: #333;
  }

  .section-description {
    font-size: 16px;
    line-height: 1.9;
    color: #666;
    max-width: 900px;
    margin: 0 auto;
  }
}

// Collections Section with Swiper
.collections-section {
  padding: 100px 80px;
  background: $white;

  .collections-title {
    text-align: center;
    font-size: 32px;
    margin-bottom: 60px;
    font-weight: 400;
    color: #333;
  }

  .swiper-container {
    position: relative;
    max-width: 1440px;
    margin: 0 auto;

    :deep(.swiper-button-prev),
    :deep(.swiper-button-next) {
      width: 40px;
      height: 40px;
      background: rgba(0, 0, 0, 0.5);
      border-radius: 50%;
      color: $white;
      display: flex;
      align-items: center;
      justify-content: center;

      &::after {
        content: '';
        display: none;
      }

      &:hover {
        background: rgba(0, 0, 0, 0.7);
      }

      svg {
        width: 20px;
        height: 20px;
        pointer-events: none;
      }
    }

    :deep(.swiper-button-prev) {
      left: -60px;
    }

    :deep(.swiper-button-next) {
      right: -60px;
      
      svg {
        transform: rotate(180deg);
      }
    }
  }

  .collection-item {
    cursor: pointer;
    transition: transform 0.3s;

    &:hover {
      transform: translateY(-10px);
    }

    .collection-image {
      margin-bottom: 20px;
      overflow: hidden;
      border-radius: 4px;

      img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        transition: transform 0.3s;
      }
    }

    &:hover .collection-image img {
      transform: scale(1.05);
    }

    .collection-name {
      text-align: center;
      font-size: 18px;
      font-weight: 400;
      color: #333;
    }
  }
}

// Fashion Section - Left Image Right Text
.fashion-section {
  display: flex;
  width: 100%;
  min-height: 600px;
  background: $white;

  .fashion-image {
    flex: 0 0 50%;
    width: 50%;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      min-height: 600px;
      object-fit: cover;
      display: block;
    }
  }

  .fashion-content {
    flex: 0 0 50%;
    width: 50%;
    background: #FFECE5;
    padding: 80px 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;

    h3 {
      font-size: 36px;
      margin-bottom: 30px;
      font-weight: 400;
      color: #333;
      line-height: 1.3;
    }

    p {
      font-size: 16px;
      line-height: 1.9;
      color: #666;
      margin-bottom: 40px;
    }

    .read-more-btn {
      background: #333;
      color: $white;
      border: none;
      padding: 15px 40px;
      font-size: 14px;
      font-weight: 400;
      cursor: pointer;
      transition: all 0.3s;
      text-transform: uppercase;
      letter-spacing: 1px;
      align-self: flex-start;

      &:hover {
        background: #000;
        transform: translateY(-2px);
      }
    }
  }
}

// Why Choose Us Section
.why-choose-section {
  padding: 100px 80px;
  background: $white;

  .section-title {
    text-align: center;
    font-size: 32px;
    margin-bottom: 60px;
    font-weight: 400;
    color: #333;
  }

  .features-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    max-width: 1440px;
    margin: 0 auto;
  }

  .feature-item {
    text-align: center;

    .feature-image {
      margin-bottom: 20px;
      overflow: hidden;
      border-radius: 4px;

      img {
        width: 100%;
        height: 400px;
        object-fit: cover;
        transition: transform 0.3s;
      }
    }

    &:hover .feature-image img {
      transform: scale(1.05);
    }

    .feature-title {
      font-size: 18px;
      font-weight: 500;
      color: #333;
      margin-bottom: 15px;
    }

    .feature-description {
      font-size: 14px;
      line-height: 1.8;
      color: #666;
    }
  }
}

// How to Working Us Section
.working-section {
  display: flex;
  width: 100%;
  min-height: 600px;
  background: $white;

  .working-content {
    flex: 0 0 50%;
    width: 50%;
    background: #FFECE5;
    padding: 80px 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;

    .working-title {
      font-size: 32px;
      color: #4A90E2;
      margin-bottom: 20px;
      font-weight: 400;
      line-height: 1.3;
    }

    .working-subtitle {
      font-size: 24px;
      color: #333;
      margin-bottom: 40px;
      font-weight: 400;
      line-height: 1.4;
    }

    .working-steps {
      display: flex;
      flex-direction: column;
      gap: 30px;
    }

    .step-item {
      display: flex;
      align-items: flex-start;
      gap: 20px;

      .step-number {
        flex-shrink: 0;
        width: 32px;
        height: 32px;
        background: #333;
        color: $white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 500;
      }

      .step-text {
        flex: 1;
        font-size: 16px;
        line-height: 1.8;
        color: #666;
        margin: 0;
        padding-top: 4px;
      }
    }
  }

  .working-image {
    flex: 0 0 50%;
    width: 50%;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      min-height: 600px;
      object-fit: cover;
      display: block;
    }
  }
}

@media (max-width: $breakpoint-mobile) {
  .home-page {
    padding-top: pxtovw(80);
  }

  .hero-section {
    .hero-content {
      flex-direction: column;
      min-height: auto;
    }

    .hero-text {
      width: 100%;
      padding: pxtovw(80) pxtovw(40);
      text-align: center;

      .hero-title {
        font-size: pxtovw(52);
        line-height: 1.3;
        margin-bottom: pxtovw(30);
      }

      .hero-description {
        font-size: pxtovw(28);
        line-height: 1.6;
      }
    }

    .hero-image {
      width: 100%;
      height: pxtovw(600);
    }
  }

  .about-section {
    padding: pxtovw(100) pxtovw(40);

    .section-title {
      font-size: pxtovw(48);
      margin-bottom: pxtovw(40);
    }

    .section-description {
      font-size: pxtovw(28);
      line-height: 1.8;
    }
  }

  .fashion-section {
    flex-direction: column;
    min-height: auto;

    .fashion-image {
      width: 100%;
      height: pxtovw(600);

      img {
        min-height: pxtovw(600);
      }
    }

    .fashion-content {
      width: 100%;
      padding: pxtovw(80) pxtovw(40);

      h3 {
        font-size: pxtovw(48);
        margin-bottom: pxtovw(30);
      }

      p {
        font-size: pxtovw(28);
        line-height: 1.7;
        margin-bottom: pxtovw(40);
      }

      .read-more-btn {
        padding: pxtovw(20) pxtovw(50);
        font-size: pxtovw(24);
      }
    }
  }

  .why-choose-section {
    padding: pxtovw(100) pxtovw(40);

    .section-title {
      font-size: pxtovw(48);
      margin-bottom: pxtovw(60);
    }

    .features-grid {
      grid-template-columns: repeat(2, 1fr);
      gap: pxtovw(40) pxtovw(30);
    }

    .feature-item {
      .feature-image img {
        height: pxtovw(400);
      }

      .feature-title {
        font-size: pxtovw(28);
        margin-bottom: pxtovw(15);
        margin-top: pxtovw(15);
      }

      .feature-description {
        font-size: pxtovw(24);
        line-height: 1.6;
      }
    }
  }

  .working-section {
    flex-direction: column;
    min-height: auto;

    .working-content {
      width: 100%;
      padding: pxtovw(80) pxtovw(40);

      .working-title {
        font-size: pxtovw(48);
        margin-bottom: pxtovw(20);
      }

      .working-subtitle {
        font-size: pxtovw(32);
        margin-bottom: pxtovw(40);
      }

      .working-steps {
        gap: pxtovw(35);
      }

      .step-item {
        .step-number {
          width: pxtovw(50);
          height: pxtovw(50);
          font-size: pxtovw(24);
        }

        .step-text {
          font-size: pxtovw(28);
          line-height: 1.6;
        }
      }
    }

    .working-image {
      width: 100%;
      height: pxtovw(600);

      img {
        min-height: pxtovw(600);
      }
    }
  }

  .collections-section{
    padding: pxtovw(100) pxtovw(40);

    .collections-title,
    .section-title {
      font-size: pxtovw(48);
      margin-bottom: pxtovw(60);
    }

    .collection-item .collection-image img,
    .product-item .product-image img {
      height: pxtovw(500);
    }

    .collection-name,
    .product-name {
      font-size: pxtovw(28);
      margin-top: pxtovw(20);
    }
  }

  .swiper-container :deep(.swiper-button-prev),
  .swiper-container :deep(.swiper-button-next) {
    display: none;
  }
}
</style>

