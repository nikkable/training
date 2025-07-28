<template>
  <div class="page page-profile">
    <div class="page__container">
      <div class="page__header">
        <h2 class="page__title">Парсинг данных</h2>
      </div>
      <div v-if="authStore.user" class="user-info">
        <h2 v-if="products.length > 0">Названия товаров</h2>
        <template v-for="product in products">
          <p>{{ product }}</p>
        </template>

        <h2>Данные для парсинга</h2>
        <div class="form__field">
          <label for="loginEmail">Url:</label>
          <input type="email" id="loginEmail" v-model="form.url" required>
        </div>

        <button class="form__button" @click="fetchParsing">Начать парсинг</button>
        <br>
        <button class="form__button" @click="fetchKafka">Test kafka</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from '~/stores/auth';
const authStore = useAuthStore();
const { $apiFetch } = useNuxtApp();

const form = ref({ url: 'https://search.wb.ru/exactmatch/ru/common/v14/search?ab_testing=false&appType=1&curr=rub&dest=12358306&hide_dtype=13;14&lang=ru&page=1&query=menu_redirect_subject_v2_8144%20%D0%BC%D1%83%D0%B6%D1%81%D0%BA%D0%B8%D0%B5%20%D0%B1%D1%80%D1%8E%D0%BA%D0%B8&resultset=catalog&sort=popular&spp=30&suppressSpellcheck=false&uclusters=2&uiv=0&uv=AQUAAQIDAAoCAAEBAAIEAAMACcHUwDa13THeNmopPEIKtjE5fcAaQO2-vDQ2vajCz0F7vW-_Rzbku2A1FMMrOxS6Lb9nukLDDzkXQCPAfz6svS--4sEZsnLDujq3QAu8BsDsPNW4pT9ntUA-rEKKMxY-TD9Cv9i4pkWsook7d696wEXA_0TUrAvA7D2FsjzAA717RPot9sAFxDU7MzwUPXqjcDkXOuK30UCmM3k6l0KiOOhBSUQERPO9msANO3qzPbjtQom_OEAktdnAukCtNTC7W8UNNgzDijigQTtA7ENGOlrBqry1O4w8CEGNude4SkG5PgVDLr_WuNWytrAovf3BIkJ_wbRChT4cN0G32TtKxU0AAAAANAA2ADYAADMRAUwm' });
const products = ref([])

const fetchParsing = async () => {
  if (!form.value.url) {
    console.warn('URL не введен.');
    return;
  }

  try {
    const data = await $apiFetch('/api/parse', {
      method: 'POST',
      body: { url: form.value.url }
    } );

    products.value = data.data;
    console.log('Данные получены:', data);
  } catch (error) {
    console.error('Ошибка при получении данных:', error);
  }
};

const fetchKafka = async () => {
  try {
    await $apiFetch('/api/send-kafka-message', {
      method: 'GET',
    } );
  } catch (error) {
    console.error('Ошибка при получении данных:', error);
  }
};
</script>

<style lang="scss">
@import '~/assets/scss/pages.scss';
</style>