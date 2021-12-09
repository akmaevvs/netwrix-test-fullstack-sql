<template>
  <div>
    <button @click="test"></button>
    <input type="text" v-model="search">
    <div style="display: flex" v-if="locCountry.length > 0 && locState.length > 0 && locPartnerLocator.length > 0">
      <div>
        <div v-for="country in locCountry" :key="country.country_id">
          {{ country.name }}
        </div>
      </div>
      <div>
        <div v-for="state in locState" :key="state.state_id">
          {{ state.name }}
        </div>
      </div>
      <div>
        <div
          v-for="partnerLocator in locPartnerLocator"
          :key="partnerLocator.country_id"
        >
          {{ partnerLocator.name }}
        </div>
      </div>
    </div>
    <div v-else>
      {{state}}
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

export default {
  name: "App",
  components: {},
  data() {
    return {
      locCountry: [],
      locState: [],
      locPartnerLocator: [],
      state: "Данные загружаются",
      search: "",
    };
  },
  async mounted() {
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
        throw new StandartError("Ошибка загрузки данных");
      } else {
        this.locCountry = locCountry;
        this.locState = locState;
        this.locPartnerLocator = locPartnerLocator;
      }
    } catch (error) {
      this.state = "Ошибка загрузки данных"

      console.log(error.message);
    }
  },
  computed: {
    filteredCompanyName() {
      return Object.entries(this.$store.state.countries).filter((item) => {
        if (this.$store.state.workerRequest.startCountry != null) {
          return item[1].name
            .toLowerCase()
            .includes(
              this.$store.state.workerRequest.startCountry.toLowerCase()
            );
        } else {
          return new Array("Начните вводить название страны");
        }
      });
    },
  },
  methods: {
    async test() {
      let a = await axios.get("http://localhost:3000/get_loc_country.php");
      console.log(a);
    },
  },
};
</script>

<style lang="scss">
#app {
}
</style>
