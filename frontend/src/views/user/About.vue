<template>
  <div class="about-page">
    <div class="about-container">
      <section class="designer-section">
        <div class="designer-image">
          <img :src="designerImage" alt="About the Designer" />
        </div>
        <div class="designer-content">
          <h2>Expert Craftsmanship, Small Studio Focus</h2>
          <p>
            <span style="font-weight: bold;color: #333">Our Story</span><br/>
            At Jstforher, we don't just manufacture veils — we craft them.<br/>
            Founded by a master artisan with over 10 years of experience in bridal millinery, our studio was built on a passion for fine lace and ethereal tulle. What began as a dedication to craftsmanship has grown into a specialized atelier serving bridal boutiques worldwide with consistent, high-quality production.
          </p>
          <p>
            <span style="font-weight: bold;color: #333">Our Studio & Craft</span><br/>
            We are not a mass-production factory.<br/>
            Our team is intentionally small — a founder and two senior artisans — each with years of hands-on experience. Every veil is made with precision and care, from stitching to lace placement and finishing details.<br/>
            This structure allows us to maintain:
            <br/>· Consistent quality across every order
            <br/>· Attention to fine details often lost in large-scale production
            <br/>· Efficient turnaround without compromising craftsmanship
            <br/>For our partners, this means reliable, boutique-level quality at scalable wholesale pricing.
          </p>
          <p>
            <span style="font-weight: bold;color: #333">Our Commitment to Partners</span><br/>
            When you work with us, you work directly with the makers.<br/>
            Every order is personally reviewed by our founder to ensure it meets our quality standards before shipment. We prioritize:
            <br/>· Clear and responsive communication
            <br/>· Consistent product quality
            <br/>· On-time delivery you can depend on
            <br/>We understand what boutiques need — not just beautiful products, but a partner they can trust.
          </p>
          <p class="see-more">
            "Every veil tells a story. After more than a decade working with lace and tulle, I still personally oversee every piece we produce. For me, this is not just a business — it's a responsibility to the boutiques and brides we serve."<br/>
            — Janet, Founder
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

