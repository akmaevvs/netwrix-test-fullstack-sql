<template>
  <div class="header-logo">
    <img class="header-logo__img" :src="logo" alt="logo" />
  </div>
</template>

<script>
import logo from "@/assets/img/Netwrix_logo_120x25.png";

export default {
  name: "header-logo",
  data() {
    return {
      logo,
    }
  }
};
</script>

<style lang="scss" scoped>
.header-logo {
  background-color: #fff;
  width: 100%;
  padding: 32px 120px;
}
@media screen and (max-width: 767px) {
  .header-logo {
    display: flex;
    justify-content: center;
    padding: 32px 0px;
  }
}
</style>
