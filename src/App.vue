<template>
  <div class="inner-container">
    <div class="header">
      <header-logo />
      <div class="header-locator">
        <div class="header-locator__inner">
          <text-block />
          <div class="filter-block">
            <div class="search-block">
              <input
                class="search-block__search-input"
                v-model="searchCompanyAddress"
                type="text"
                :placeholder="placeholderText"
              />
              <img
                @click="SendQuery"
                class="search-block__search-ico"
                :src="searchIco"
              />
            </div>
            <div class="button-block">
              <button
                class="button-block__filter-button"
                @click.self="showTypes = !showTypes"
                @blur="showTypes = false"
              >
                <template v-if="searchPartnerType">
                  {{ searchPartnerType }}
                </template>
                <template v-else> Type </template>
                <img class="button-block__arrow-down" :src="arrowDown" />
                <transition name="slide-fade">
                  <div v-if="showTypes" class="types-block">
                    <button
                      @click="this.searchPartnerType = ''"
                      class="types-block__type-button"
                    >
                      Type
                    </button>
                    <button
                      v-for="(type, index) in locPartnerTypes"
                      :key="index"
                      @click="this.searchPartnerType = type; SendQuery()"
                      class="types-block__type-button"
                    >
                      {{ type }}
                    </button>
                  </div>
                </transition>
              </button>
              <div class="right-buttons">
                <button
                  @click.self="showCountries = !showCountries"
                  class="button-block__filter-button button-block__filter-button--left"
                >
                  <template v-if="searchCountry">
                    {{ searchCountry }}
                  </template>
                  <template v-else> Country </template>
                  <img class="button-block__arrow-down" :src="arrowDown" />
                  <transition name="slide-fade">
                    <div v-if="showCountries" class="countries-block">
                      <input
                        type="text"
                        class="countries-search"
                        v-model="searchCountry"
                      />
                      <div class="types-block types-block--scroll">
                        <button
                          v-for="country in filteredCountries"
                          :key="country.country_id"
                          @click="
                            this.searchCountry = country.name;
                            showCountries = false;
                          "
                          class="types-block__type-button"
                        >
                          {{ country.name }}
                        </button>
                      </div>
                    </div>
                  </transition>
                </button>
                <button
                  @click.self="showStates = !showStates"
                  class="button-block__filter-button button-block__filter-button--right"
                >
                  <template v-if="searchState">{{ searchState }}</template>
                  <template v-else>State</template>
                  <img class="button-block__arrow-down" :src="arrowDown" />
                  <transition name="slide-fade">
                    <div v-if="showStates" class="countries-block">
                      <input
                        type="text"
                        class="countries-search"
                        v-model="searchState"
                      />
                      <div class="types-block types-block--scroll">
                        <button
                          v-for="state in filteredState"
                          :key="state.state_id"
                          @click="
                            this.searchState = state.name;
                            showStates = false;
                          "
                          class="types-block__type-button"
                        >
                          {{ state.name }}
                        </button>
                      </div>
                    </div>
                  </transition>
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
        v-if="
          locCountry.length > 0 &&
          locState.length > 0 &&
          locPartnerLocator.length > 0
        "
      >
        <transition-group name="list-complete">
          <partner-card
            class="partner-group"
            v-for="partnerLocator in locPartnerLocator"
            v-bind:key="partnerLocator.country_id"
            :partnerLocator="partnerLocator"
          />
        </transition-group>
      </div>
      <transition name="fade">
        <div v-if="loading" class="status-container">
          <div class="status-container__content">{{ state }}</div>
        </div>
      </transition>
      <div
        class="static-status-container"
        v-if="
          locCountry.length == 0 &&
          locState.length == 0 &&
          locPartnerLocator.length == 0 &&
          !loading
        "
      >
        {{ state }}
      </div>
      <div
        class="static-status-container"
        v-else-if="!loading && this.locPartnerLocator.length == 0"
      >
        {{ state }}
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
import TextBlock from "@/components/TextBlock.vue";
import HeaderLogo from "@/components/HeaderLogo.vue";
import PartnerCard from "@/components/PartnerCard.vue";
import axios from "axios";
import searchIco from "@/assets/img/search-ico.png";
import arrowDown from "@/assets/img/arrow_down.png";
export default {
  name: "App",
  components: {
    TextBlock,
    PartnerCard,
    HeaderLogo,
  },
  data() {
    return {
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
      locPartnerTypes: new Set([]),
      searchPartnerType: "",
      state: "Please wait ... ",
      searchCompanyAddress: "",
      showTypes: false,
      loading: false,
      showCountries: false,
      searchCountry: "",
      showStates: false,
      searchState: "",
    };
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.OnResize);
  },
  async mounted() {
    try {
      this.loading = true;
      this.$nextTick(() => {
        window.addEventListener("resize", this.OnResize);
      });
      await this.Sleep(2000).then(() => {}); // forced stop for loading effect
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
        throw new StandartError(
          "Your search parameters did not match any partners. Please try different search."
        );
      } else {
        this.locCountry = locCountry;
        this.locState = locState;
        this.locPartnerLocator = locPartnerLocator;
        this.locPartnerLocator.filter((partner) => {
          this.locPartnerTypes.add(partner.status);
        });
      }
      this.loading = false;
    } catch (error) {
      this.state = error.message;
      setTimeout(() => {
        this.loading = false;
      }, 2000);
    }
  },
  computed: {
    filteredState() {
      return this.locState.filter((state) => {
        if (this.searchState && this.searchState != "Satate") {
          if (
            state.name.toLowerCase().includes(this.searchState.toLowerCase())
          ) {
            return state;
          }
        } else {
          return state;
        }
      });
    },
    filteredCountries() {
      return this.locCountry.filter((country) => {
        if (this.searchCountry && this.searchCountry != "Country") {
          if (
            country.name
              .toLowerCase()
              .includes(this.searchCountry.toLowerCase())
          ) {
            return country;
          }
        } else {
          return country;
        }
      });
    },
  },
  methods: {
    async SendQuery() {
      try {
        if (!this.searchCompanyAddress && !this.searchPartnerType) {
          this.loading = true;

          throw new StandartError("Empty search input ...");
        }
        document.body.style.overflow = "hidden";

        this.state = "Please wait ...";

        this.loading = true;

        const body = {
          type: this.searchPartnerType,
          company_address: this.searchCompanyAddress,
        };
        let locPartnerLocator = await (
          await axios.get(
            `http://localhost:3000/get_partner_locator_with_query.php?type=${body.type}&company_address=${body.company_address}`
          )
        ).data;
        if (locPartnerLocator.error) {
          throw new StandartError(
            "Your search parameters did not match any partners. Please try different search."
          );
        } else {
          this.locPartnerLocator = locPartnerLocator;
          if (this.locPartnerLocator.length == 0) {
            this.state =
              "Your search parameters did not match any partners. Please try different search.";
          }
          setTimeout(() => {
            this.loading = false;
            document.body.style.overflow = "visible";
          }, 2000);
        }
      } catch (error) {
        document.body.style.overflow = "hidden";
        this.state = error.message;
        setTimeout(() => {
          this.loading = false;
          document.body.style.overflow = "visible";
        }, 2000);
      }
    },
    Sleep(ms) {
      return new Promise((resolve) => setTimeout(resolve, ms));
    },
    OnResize() {
      this.placeholderText =
        window.innerWidth > 767
          ? "Search by company name or adress.."
          : "Search";
    },
  },
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
        cursor: pointer;
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
        & .countries-search {
          height: 30px;
          margin: 3px;
          padding: 4px;
          border: 1px solid #b7bcc1;
          border-radius: 2px;
        }
        & .types-block,
        .countries-block {
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

          &--scroll {
            position: relative;
            top: 0;
            border: none;

            max-height: 300px;
            overflow-y: scroll;
          }

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
  & .partner-group {
    width: 100%;
    display: flex;
    justify-content: center;
  }
  & .static-status-container {
    display: flex;
    text-align: center;
    justify-content: center;
    margin-top: 30px;
  }
  & .status-container {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 20px;
    background: rgba(0, 0, 0, 0.301);
    height: 100vh;

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
}

@media screen and (max-width: 767px) {
  .header-locator__inner {
    width: 100%;
    max-width: 288px;
    margin: 0 auto;
    padding: 65px 0px;
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
}
</style>
