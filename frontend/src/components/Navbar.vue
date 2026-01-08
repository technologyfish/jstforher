<template>
  <nav class="navbar">
    <div class="navbar-container">
      <!-- Mobile Menu Button (Left) -->
      <button class="mobile-menu-btn" @click="toggleMenu" aria-label="Menu">
        <span class="hamburger-icon">
          <span></span>
          <span></span>
          <span></span>
        </span>
      </button>

      <!-- Left Navigation Menu (Desktop) -->
      <ul class="nav-menu nav-left">
        <li><router-link to="/" :class="{ active: currentRoute === '/' }">Home</router-link></li>
        <li><router-link to="/lookbook" :class="{ active: currentRoute === '/lookbook' }">Lookbook</router-link></li>
        <li><router-link to="/about" :class="{ active: currentRoute === '/about' }">About</router-link></li>
        <li><router-link to="/contact" :class="{ active: currentRoute === '/contact' }">Contact</router-link></li>
      </ul>

      <!-- Center Logo -->
      <router-link to="/" class="logo-center">
        <img src="@/assets/images/logo.png" alt="NEA·MILANO" class="logo-image" />
      </router-link>

      <!-- Right Social Icons (Desktop) -->
      <div class="social-icons">
        <a href="https://instagram.com" target="_blank" class="icon-link">
          <img src="@/assets/images/instagram.png" alt="Instagram" />
        </a>
        <a href="https://facebook.com" target="_blank" class="icon-link">
          <img src="@/assets/images/facebook.png" alt="Facebook" />
        </a>
      </div>
    </div>

    <!-- Mobile Sidebar Menu -->
    <transition name="slide">
      <div v-if="menuOpen" class="mobile-sidebar-overlay" @click="closeMenu">
        <div class="mobile-sidebar" @click.stop>
          <div class="sidebar-header">
            <button class="close-btn" @click="closeMenu" aria-label="Close">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
              </svg>
            </button>
          </div>
          
          <ul class="mobile-nav-menu">
            <li><router-link to="/" @click="closeMenu" :class="{ active: currentRoute === '/' }">Home</router-link></li>
            <li><router-link to="/lookbook" @click="closeMenu" :class="{ active: currentRoute === '/lookbook' }">Lookbook</router-link></li>
            <li><router-link to="/about" @click="closeMenu" :class="{ active: currentRoute === '/about' }">About</router-link></li>
            <li><router-link to="/contact" @click="closeMenu" :class="{ active: currentRoute === '/contact' }">Contact</router-link></li>
          </ul>

          <div class="sidebar-social">
            <a href="https://instagram.com" target="_blank" class="icon-link">
              <img src="@/assets/images/instagram.png" alt="Instagram" />
            </a>
            <a href="https://facebook.com" target="_blank" class="icon-link">
              <img src="@/assets/images/facebook.png" alt="Facebook" />
            </a>
          </div>
        </div>
      </div>
    </transition>
  </nav>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const currentRoute = computed(() => route.path)
const menuOpen = ref(false)

const toggleMenu = () => {
  menuOpen.value = !menuOpen.value
  document.body.style.overflow = menuOpen.value ? 'hidden' : ''
}

const closeMenu = () => {
  menuOpen.value = false
  document.body.style.overflow = ''
}

// 路由变化时关闭菜单
watch(currentRoute, () => {
  closeMenu()
})
</script>

<style lang="scss" scoped>
.navbar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: $white;
  z-index: 1000;
  padding: 20px 0;
  
  @media (max-width: $breakpoint-mobile) {
    padding: pxtovw(20) 0;
  }
}

.navbar-container {
  max-width: 1920px;
  margin: 0 auto;
  padding: 0 60px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  
  @media (max-width: $breakpoint-mobile) {
    padding: 0 pxtovw(30);
  }
}

// Mobile Menu Button (左侧汉堡菜单)
.mobile-menu-btn {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  padding: 0;
  z-index: 10;
  
  @media (max-width: $breakpoint-mobile) {
    display: block;
  }
}

.hamburger-icon {
  display: flex;
  flex-direction: column;
  gap: pxtovw(6);
  width: pxtovw(28);
  
  span {
    display: block;
    width: 100%;
    height: pxtovw(2);
    background: #333;
    transition: all 0.3s;
  }
}

// 左侧导航 (Desktop)
.nav-left {
  display: flex;
  gap: 35px;
  list-style: none;
  margin: 0;
  padding: 0;
  flex: 1;

  li {
    a {
      text-decoration: none;
      color: $text-color;
      font-size: 14px;
      transition: color 0.3s;
      position: relative;
      text-transform: capitalize;
      font-weight: 400;

      &:hover,
      &.active {
        color: $primary-color;
      }
    }
  }
  
  @media (max-width: $breakpoint-mobile) {
    display: none;
  }
}

// 中间 Logo
.logo-center {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  align-items: center;
  text-decoration: none;

  .logo-image {
    height: 64px;
    width: auto;
    object-fit: contain;
  }
  
  @media (max-width: $breakpoint-mobile) {
    .logo-image {
      height: pxtovw(64);
    }
  }
}

// 右侧社交图标 (Desktop)
.social-icons {
  display: flex;
  gap: 20px;
  align-items: center;
  flex: 1;
  justify-content: flex-end;

  .icon-link {
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: opacity 0.3s;

    &:hover {
      opacity: 0.7;
    }

    img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }
  }
  
  @media (max-width: $breakpoint-mobile) {
    display: none;
  }
}

// Mobile Sidebar Overlay
.mobile-sidebar-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  z-index: 999;
  
  @media (min-width: 769px) {
    display: none;
  }
}

// Mobile Sidebar
.mobile-sidebar {
  position: fixed;
  top: 0;
  left: 0;
  bottom: 0;
  width: pxtovw(500);
  max-width: 85%;
  background: white;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
  padding: pxtovw(40) pxtovw(30);
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

.sidebar-header {
  display: flex;
  justify-content: flex-end;
  margin-bottom: pxtovw(40);
  
  .close-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    color: #333;
    
    svg {
      width: pxtovw(28);
      height: pxtovw(28);
    }
  }
}

.mobile-nav-menu {
  list-style: none;
  margin: 0;
  padding: 0;
  flex: 1;
  
  li {
    margin-bottom: pxtovw(30);
    
    a {
      display: block;
      text-decoration: none;
      color: $text-color;
      font-size: pxtovw(32);
      font-weight: 400;
      text-transform: capitalize;
      transition: color 0.3s;
      padding: pxtovw(10) 0;
      
      &:hover,
      &.active {
        color: $primary-color;
      }
    }
  }
}

.sidebar-social {
  display: flex;
  gap: pxtovw(30);
  align-items: center;
  padding-top: pxtovw(40);
  border-top: 1px solid #e5e5e5;
  
  .icon-link {
    width: pxtovw(40);
    height: pxtovw(40);
    display: flex;
    align-items: center;
    justify-content: center;
    
    img {
      width: 100%;
      height: 100%;
      object-fit: contain;
    }
  }
}

// Slide Transition
.slide-enter-active,
.slide-leave-active {
  transition: opacity 0.3s;
  
  .mobile-sidebar {
    transition: transform 0.3s ease;
  }
}

.slide-enter-from,
.slide-leave-to {
  opacity: 0;
  
  .mobile-sidebar {
    transform: translateX(-100%);
  }
}

.slide-enter-to,
.slide-leave-from {
  opacity: 1;
  
  .mobile-sidebar {
    transform: translateX(0);
  }
}
</style>

