<template>
  <div class="about-page">
    <div class="about-container">
      <section class="designer-section">
        <div class="designer-image">
          <img :src="designerImage" alt="About the Designer" />
        </div>
        <div class="designer-content">
          <h2>Expert Craftsmanship, Small Studio Heart</h2>
          <p>

            <span style="font-weight: bold;color: #333">The Story</span> <br/>At Jstforher, we don’t just manufacture veils; we craft them.Our founder is a master artisan with over 10 years of experience in the delicate art of bridal millinery. What started as a passion for fine lace and ethereal tulle has grown into a specialized studio dedicated to the highest standards of quality.           </p>
          <p>
            <span style="font-weight: bold;color: #333">The Team & Artistry</span> <br/>We are not a mass-production factory.Our studio is a close-knit team of three: our founder and two highly skilled senior tailors. Every stitch, every bead, and every lace appliqué is handled by experts who understand the soul of a garment. Unlike assembly-line products, each Jstforher veil is treated as a piece of art. Because we are small, we are meticulous. Because we are experts, we are efficient. This allows us to offer couture-level quality at a price that makes sense for independent boutiques.
          </p>
         <p>
           <span style="font-weight: bold;color: #333">Our Commitment</span> <br/> Personal Responsibility, Professional Results.When you partner with us, you are speaking directly to the makers. Our founder personally oversees the quality control of every single order, ensuring that what reaches your boutique is nothing short of perfection. We take pride in our reliability, our transparent communication, and our unwavering commitment to your success.
         </p>

          <p class="see-more">
            "Every veil tells a story. After a decade of working with delicate tulle and lace, I still personally oversee every piece that leaves our studio. To me, it’s not just a business—it’s a commitment to your boutique and your brides." — Janet, Founder of Jstforher
          </p>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import designerImage from '@/assets/images/img-7.png'
</script>

<style lang="scss" scoped>
.about-page {
  min-height: 100vh;
  padding-top: 70px;
  background: $white;
}

.about-container {
  max-width: 100%;
  margin: 0 auto;
  padding: 0;
}

.designer-section {
  display: flex;
  min-height: 700px;
  align-items: stretch;

  .designer-image {
    flex: 0 0 50%;
    width: 50%;
    overflow: hidden;

    img {
      width: 100%;
      height: 100%;
      min-height: 700px;
      object-fit: cover;
      display: block;
    }
  }

  .designer-content {
    flex: 0 0 50%;
    width: 50%;
    background: #F5F5F5;
    padding: 100px 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;

    h2 {
      font-size: 36px;
      margin-bottom: 40px;
      font-weight: 400;
      color: #333;
      line-height: 1.3;
    }

    p {
      font-size: 16px;
      line-height: 1.9;
      color: #666;
      margin-bottom: 25px;

      &:last-of-type {
        margin-bottom: 0;
      }
    }

    .see-more {
      margin-top: 15px;
      
      .link {
        color: #333;
        text-decoration: underline;
        transition: color 0.3s;

        &:hover {
          color: #666;
        }
      }
    }
  }
}

@media (max-width: $breakpoint-mobile) {
  .about-page {
    padding-top: pxtovw(80);
  }

  .designer-section {
    flex-direction: column;
    min-height: auto;

    .designer-image {
      width: 100%;
      height: pxtovw(600);

      img {
        min-height: pxtovw(600);
      }
    }

    .designer-content {
      width: 100%;
      padding: pxtovw(80) pxtovw(40);

      h2 {
        font-size: pxtovw(48);
        line-height: 1.3;
        margin-bottom: pxtovw(40);
      }

      p {
        font-size: pxtovw(28);
        line-height: 1.8;
        margin-bottom: pxtovw(30);
      }

      .see-more {
        margin-top: pxtovw(20);
        font-size: pxtovw(28);
      }
    }
  }
}
</style>

