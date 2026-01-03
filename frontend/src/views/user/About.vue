<template>
  <div class="about-page">
    <div class="about-container">
      <section class="designer-section">
        <div class="designer-image">
          <img :src="designerImage" alt="About the Designer" />
        </div>
        <div class="designer-content">
          <h2>About the Designer</h2>
          <p>
            Anastasia Emelina is a heart and soul behind NEA • MILANO. She is a Russian-born, Italian-based stylist and jewelry designer. After having graduated with a degree in design and art history in St. Petersburg, she moved to Milan.
          </p>
          <p>
            In 2018, she completed her studies with a course in brand design and advertising. Following her dream to make her living as an artisan, she went on to create her own bridal accessory brand with the goal of introducing it to brides, a modern yet romantic interpretation of accessories. She works out of her personalised home studio in Milan. She is most known for her metal adornments, but she is able to keep her creativity flowing by always introducing new materials and techniques. She loves photography and enjoys creating and styling fine art sets for wedding editorials and brands.
          </p>
          <p class="see-more">
            See more of her work <a href="#" class="link">here</a>.
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

