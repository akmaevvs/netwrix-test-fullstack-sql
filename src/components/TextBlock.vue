<template>
  <div class="text-block">
    <h2 class="text-block__title">Netwrix Partner Locator</h2>
    <div class="text-block__regular-text-block">
      <p class="text-block__text">
        Hundreds of Netwrix partners around the world are standing by to help
        you.
      </p>
      <p class="text-block__text">
        With our Partner Locator you can easily find the list of authorized
        partners in your area.
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: "text-block",
};
</script>

<style lang="scss" scoped>
.text-block {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  gap: 21px;

  &__regular-text-block {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  &__title {
    // font-size: 46px;
    // line-height: 28px;
  }

  &__text {
    font-size: 16px;
    line-height: 32px;
  }
}
@media screen and (max-width: 767px) {
  .text-block__title {
    font-style: normal;
    font-weight: bold;
    font-size: 30px;
    line-height: 40px;
    max-width: 220px;
  }
}
@media screen and (min-width: 768px) {
  .text-block__title {
    font-size: 46px;
    line-height: 28px;
  }
}
</style>
