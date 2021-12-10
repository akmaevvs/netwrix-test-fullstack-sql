<template>
  <div class="partner-card">
    <div class="left-part">
      <div class="logo-block">
        <img class="logo-block__logo" :src="partnerLocator.logo" alt />
      </div>
      <div class="info-block">
        <h4 class="info-block__company">
          {{ partnerLocator.company }}
        </h4>
        <p class="info-block__address">{{ partnerLocator.address }}</p>
      </div>
    </div>
    <div class="separator-horizontal"></div>
    <div class="right-part">
      <div class="contact-block">
        <a
          class="contact-block__website"
          :href="partnerLocator.website"
          target="_blank"
          >Website</a
        >
        <a class="contact-block__phone" :href="`tel:${partnerLocator.phone}`">{{
          partnerLocator.phone
        }}</a>
      </div>
      <div class="separator-vertical"></div>
      <div class="status-block">
        <p class="status-block__status">{{ partnerLocator.status }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "partner-card",
  props: {
    partnerLocator: {},
  },
};
</script>

<style lang="scss" scoped>
.partner-card {
  display: flex;
  width: 100%;
  position: relative;

  justify-content: space-between;
  background-color: #fff;
  box-shadow: 0px 1px 2px rgba(5, 27, 43, 0.14);
  // padding: 35px 78px;
  border-radius: 4px;
  align-items: center;
  // gap: 66px;

  & .left-part,
  .right-part {
    display: flex;
    // align-items: center;
  }
  & .left-part {
    flex: 66;
  }

  & .right-part {
    flex: 34;
    display: flex;
  }

  & .info-block,
  .contact-block,
  .status-block,
  .logo-block {
    display: flex;
    flex-direction: column;
    // justify-content: center;
  }
  & .logo-block {
    // padding: 35px 78px;
    padding: 5% 11%;
  }
  & .info-block {
    // justify-content: space-between;
    gap: 14px;
    &__company {
      font-size: 21px;
      line-height: 32px;
    }
    &__address {
      font-size: 14px;
      line-height: 24px;
    }
  }
  & .contact-block {
    flex: 40;
    // padding-right: 5%;

    &__website {
      color: #0068da;
      font-size: 14px;
      line-height: 32px;
    }

    &__phone {
      font-size: 13px;
      line-height: 32px;
      color: var(--default-text-color);
      text-decoration: none;
    }
  }
  & .separator-vertical {
    width: 1px;
    height: 58px;
    background-color: #dcdee0;
  }

  & .status-block {
    flex: 60;
    // padding-left: 5%;
    &__status {
      font-size: 13px;
      line-height: 32px;
      color: #354556;
    }
  }
}
@media screen and (max-width: 767px) {
  .partner-card {
    flex-direction: column;
    max-width: 270px;
    padding: 43px 24px;
    gap: 33px;
    position: relative;

    & .separator-vertical {
      display: none;
    }

    & .left-part {
      flex-direction: column;
      gap: 40px;
      align-self: start;

      & .logo-block {
        align-self: flex-start;
        padding: 0;
      }
    }

    & .right-part {
      flex-direction: column;
      align-items: flex-start;
      align-self: flex-start;
    }

    & .status-block {
      padding-left: 0;
    }
  }
}
@media screen and (min-width: 768px) {
  .separator-horizontal {
    display: none;
  }
  .right-part {
    align-items: center;
  }
  .status-block {
    padding-left: 5%;
  }
  .info-block {
    justify-content: center;
  }
  .partner-card {
    gap: 66px;
  }
}
</style>
