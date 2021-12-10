<template>
  <div class="inner-container">
    <div class="header">
      <div class="header-logo">
        <img class="header-logo__img" :src="logo" alt="logo" />
      </div>
      <div class="header-locator">
        <div class="header-locator__inner">
          <div class="text-block">
            <h2 class="text-block__title">Netwrix Partner Locator</h2>
            <div class="text-block__regular-text-block">
              <p
                class="text-block__text"
              >Hundreds of Netwrix partners around the world are standing by to help you.</p>
              <p
                class="text-block__text"
              >With our Partner Locator you can easily find the list of authorized partners in your area.</p>
            </div>
          </div>
          <div class="filter-block">
            <div class="search-block">
              <input
                class="search-block__search-input"
                v-model="searchCompanyAddress"
                type="text"
                :placeholder="placeholderText"
              />
              <img class="search-block__search-ico" :src="searchIco" />
            </div>
            <div class="button-block">
              <button
                class="button-block__filter-button"
                @click.self="showTypes = !showTypes"
                @blur="showTypes = false"
              >
                {{searchPartnerType}}
                <img class="button-block__arrow-down" :src="arrowDown" />
                <transition name="slide-fade">
                  <div v-if="showTypes" class="types-block">
                    <button
                      v-for="(type, index) in locPartnerTypes"
                      :key="index"
                      @click="SetSearchType(type)"
                      class="types-block__type-button"
                    >{{type}}</button>
                  </div>
                </transition>
              </button>
              <div class="right-buttons">
                <button class="button-block__filter-button button-block__filter-button--left">
                  Country
                  <img class="button-block__arrow-down" :src="arrowDown" />
                </button>
                <button class="button-block__filter-button button-block__filter-button--right">
                  State
                  <img class="button-block__arrow-down" :src="arrowDown" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="content">
      <div
        class="content-container"
        v-if="locCountry.length > 0 && locState.length > 0 && locPartnerLocator.length > 0"
      >
        <!-- <div>
          <div v-for="country in locCountry" :key="country.country_id">{{ country.name }}</div>
        </div>
        <div>
          <div v-for="state in locState" :key="state.state_id">{{ state.name }}</div>
        </div>-->
        <transition-group name="list-complete">
          <div
            class="partner-card"
            v-for="partnerLocator in filteredCompanies"
            :key="partnerLocator.country_id"
          >
            <div class="left-part">
              <div class="logo-block">
                <img class="logo-block__logo" :src="partnerLocator.logo" alt />
              </div>
              <div class="info-block">
                <h4 class="info-block__company">{{partnerLocator.company}}</h4>
                <p class="info-block__address">{{partnerLocator.address}}</p>
              </div>
            </div>
            <div class="separator-horizontal"></div>
            <div class="right-part">
              <div class="contact-block">
                <a
                  class="contact-block__website"
                  :href="partnerLocator.website"
                  target="_blank"
                >Website</a>
                <a
                  class="contact-block__phone"
                  :href="`tel:${partnerLocator.phone}`"
                >{{partnerLocator.phone}}</a>
              </div>
              <div class="separator-vertical"></div>
              <div class="status-block">
                <p class="status-block__status">{{partnerLocator.status}}</p>
              </div>
            </div>
          </div>
        </transition-group>
      </div>
      <transition name="fade">
        <div v-if="loading" class="status-container">
          <div class="status-container__content">{{state}}</div>
        </div>
      </transition>
      <div class="static-status-container"
        v-if="(locCountry.length == 0 && locState.length == 0 && locPartnerLocator.length == 0) && !loading"
      >
        {{state}}
      </div>
      <div class="static-status-container" v-else-if="!loading && filteredCompanies.length == 0">
        {{state}}

      </div>
    </div>
  </div>
</template>

<script>
class StandartError extends Error {
  constructor(message = "Ошибка") {
    super(message);
    this.name = "StandartError";
  }
}

import axios from "axios";
import logo from "@/assets/img/Netwrix_logo_120x25.png";
import searchIco from "@/assets/img/search-ico.png";
import arrowDown from "@/assets/img/arrow_down.png";
export default {
  name: "App",
  components: {},
  data() {
    return {
      logo,
      searchIco,
      placeholderText:
        window.innerWidth > 767
          ? "Search by company name or adress.."
          : "Search",
      windowWidth: window.innerWidth,
      arrowDown,
      locCountry: [],
      locState: [],
      locPartnerLocator: [],
      locPartnerTypes: new Set(["Type"]),
      searchPartnerType: "Type",
      state: "Please wait ... ",
      searchCompanyAddress: "",
      showTypes: false,
      loading: false
    };
  },
  watch: {
    filteredCompanies: {
      deep: true,
      handler() {
        if (this.filteredCompanies == 0) {
          this.state = "Your search parameters did not match any partners. Please try different search."
        }
      }
    }
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.onResize);
  },
  async mounted() {
    this.loading = true;
    this.$nextTick(() => {
      window.addEventListener("resize", this.onResize);
    });
    try {
      let locCountry = await (
        await axios.get("http://localhost:3000/get_loc_country.php")
      ).data;
      let locState = await (
        await axios.get("http://localhost:3000/get_loc_state.php")
      ).data;
      let locPartnerLocator = await (
        await axios.get("http://localhost:3000/get_partner_locator.php")
      ).data;

      if (locCountry.error || locState.error || locPartnerLocator.error) {
        throw new StandartError("Your search parameters did not match any partners. Please try different search.");
      } else {
        this.locCountry = locCountry;
        this.locState = locState;
        this.locPartnerLocator = locPartnerLocator;
        this.locPartnerLocator.filter(partner => {
          this.locPartnerTypes.add(partner.status);
        });
        console.log(this.locPartnerTypes);
      }
      this.loading = false;
    } catch (error) {
      this.state = "Your search parameters did not match any partners. Please try different search.";
      setTimeout(() => {
        this.loading = false;
      }, 2000);

      console.log(error.message);
    }
  },
  computed: {
    filteredCompanies() {
      return this.locPartnerLocator.filter(partner => {
        if (this.searchCompanyAddress) {
          if (
            partner.company
              .toLowerCase()
              .includes(this.searchCompanyAddress.toLowerCase()) ||
            partner.address
              .toLowerCase()
              .includes(this.searchCompanyAddress.toLowerCase())
          ) {
            return partner;
          }
        } else if (this.searchPartnerType !== "Type") {
          if (
            partner.status
              .toLowerCase()
              .includes(this.searchPartnerType.toLowerCase())
          ) {
            return partner;
          }
        } else {
          return partner;
        }
      });
    }
    // filteredTypes() {

    // }
  },
  methods: {
    SetSearchType(type) {
      this.searchPartnerType = type;
      console.log(type);

      // this.filteredCompanies = this.locPartnerLocator.filter(partner => {
      //   if (partner.status.toLowerCase() === type.toLowerCase()) {
      //     return partner;
      //   }
      // });
    },
    onResize() {
      this.placeholderText =
        window.innerWidth > 767
          ? "Search by company name or adress.."
          : "Search";
    },
    async test() {
      let a = await axios.get("http://localhost:3000/get_loc_country.php");
      console.log(a);
    }
  }
};
</script>

<style lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Open+Sans&display=swap");

:root {
  --default-text-color: #354556;
  --white-text-color: #fff;
  --placeholder-text-color: #7f8893;
  --default-button-text-color: #b4b4b4;
  --input-label-text-color: #49485e;
  --font-size-button-input: 12px;
  --default-font-family: "Open Sans", sans-serif;
}
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}
body {
  position: relative;
  background-color: #f2f2f2;
  // overflow-x: hidden;
}
input,
button {
  outline: none;
  border: none;
  background: none;
  padding: 0;
  font-family: var(--default-font-family);
}
input::placeholder {
  font-family: var(--default-font-family);
  color: var(--placeholder-text-color);
}
#app {
  font-family: var(--default-font-family);
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #3f3f3f;
}
.flip-list-move {
  transition: transform 1s;
}

// fade effect
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease-in-out;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

// add/delete product
.list-complete-enter, .list-complete-leave-to
/* .list-complete-leave-active до версии 2.1.8 */ {
  opacity: 0;
  transform: translateY(30px);
}
.list-complete-leave-active {
  position: absolute;
}

// appearence products
.slide-fade-enter-active,
.slide-fade-leave-active {
  transition: all 0.5s ease-in-out;
}
.slide-fade-enter-from,
.slide-fade-leave-to {
  transform: translateY(-100%);
  opacity: 0;
}

.inner-container {
  margin-bottom: 80px;
}
.header {
  & .header-logo {
    background-color: #fff;
    width: 100%;
    padding: 32px 120px;
  }
  & .header-locator {
    background-color: #0973ba;
    color: var(--white-text-color);
    background-image: url("~@/assets/img/bg_large_1.png");

    &__inner {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 120px 0px;
      gap: 48px;
    }
    & .search-block {
      position: relative;
      width: 100%;
      &__search-input {
        width: 100%;
        background-color: #fff;
        padding: 6.5px 16px;
        font-size: 15px;
        line-height: 32px;
        border-radius: 6px;
        position: relative;
      }
      &__search-ico {
        position: absolute;
        right: 13px;
        top: 15px;
      }
    }
    & .filter-block {
      max-width: 621px;
      width: 100%;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      gap: 20px;
    }

    & .text-block {
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

    & .button-block {
      display: flex;
      width: 100%;
      position: relative;
      // gap: 12px;

      & .right-buttons {
        flex: 2;
        display: flex;
        justify-content: space-between;
      }

      &__filter-button {
        position: relative;
        flex: 1;
        background: rgba(0, 0, 0, 0.0001);
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #fff;
        cursor: pointer;
        font-size: 13px;
        & .types-block {
          position: absolute;
          display: flex;
          flex-direction: column;
          background-color: #fff;
          border-radius: 0px 0px 3px 3px;
          border: 1px solid #b7bcc1;
          top: 35px;
          width: 100%;
          left: 0;
          z-index: 3;

          &__type-button {
            cursor: pointer;
            padding: 10px 10px;
            align-self: flex-start;
            transition: all 0.2s ease-in-out;
            width: 100%;
            text-align: start;
            &:hover {
              background-color: #0068da;
              color: #fff;
            }
          }
        }
        // &--right {
        //   border-radius: 0px 6px 6px 0px;
        //   border-left: 1px solid #fff;
        // }

        // &--left {
        //   border-radius: 6px 0px 0px 6px;
        //   border-right: 1px solid #fff;
        // }
      }
    }
  }
}

.content {
  & .content-container {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 20px;
    max-width: 1110px;
    padding: 0px 10px;
    margin: 53px auto;
    position: relative;
  }
  & .static-status-container {
    display: flex;
    text-align: center;
    justify-content: center;
    margin-top: 30px;
  }
  & .status-container {
    position: absolute;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 20px;
    background: rgba(0, 0, 0, 0.301);

    &__content {
      background: #fff;
      min-height: 140px;
      width: 40%;
      min-width: 280px;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      border-radius: 5px;
      box-shadow: 0px 1px 2px rgba(5, 27, 43, 0.14);
      padding: 30px;
    }
  }
  & .partner-card {
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
}

@media screen and (max-width: 767px) {
  .header-logo {
    display: flex;
    justify-content: center;
    padding: 32px 0px;
  }
  .header-locator__inner {
    width: 100%;
    max-width: 288px;
    margin: 0 auto;
    padding: 65px 0px;
  }
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
  .text-block__title {
    font-style: normal;
    font-weight: bold;
    font-size: 30px;
    line-height: 40px;
    max-width: 220px;
  }
  .button-block,
  .right-buttons {
    flex-direction: column;
    gap: 8px;
  }
  .button-block__filter-button {
    border: 2px solid #ffffff;
    border-radius: 6px;
  }
  .button-block__filter-button--right,
  .button-block__filter-button--left {
    border-radius: 6px;
    border: 2px solid #fff;
  }
  .separator-horizontal {
    display: block;
    height: 1px;
    width: 100%;
    background-color: #dcdee0;
  }
}
@media screen and (min-width: 768px) {
  .separator-horizontal {
    display: none;
  }
  .text-block__title {
    font-size: 46px;
    line-height: 28px;
  }
  .button-block {
    gap: 12px;
  }
  .button-block__filter-button {
    border: 2px solid #ffffff;
    border-radius: 6px;

    &--right {
      border-radius: 0px 6px 6px 0px;
      border-left: 1px solid #fff;
    }

    &--left {
      border-radius: 6px 0px 0px 6px;
      border-right: 1px solid #fff;
    }
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
