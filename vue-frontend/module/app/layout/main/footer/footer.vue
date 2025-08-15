<template>

  <div>

    <div class="container q-mt-md q-mb-md flex">

      <div style="max-width: 300px; align-self: end;">

        <div class="row q-col-gutter-x-md">

          <div class="col-12">
            <a href="https://pos.gosuslugi.ru/og/org-activities?reg_code=25&utm_source=vk1&utm_medium=25&utm_campaign=1183850008760"
               target="_blank"><img src="/statics/pos/opinion.png"/></a>
          </div>

          <div class="col-12">
            <a href="https://pos.gosuslugi.ru/form/?opaId=217359&utm_source=vk&utm_medium=25&utm_campaign=1183850008760"
               target="_blank"><img src="/statics/pos/problem.png"/></a>
          </div>

        </div>

      </div>

    </div>

    <div class="com">

      <q-dialog v-model="cookieDialog" position="bottom" seamless>
        <q-card class="" style="max-width: 1324px;background-color:#fff8c4;">

          <q-card-section class="" @click="onCookieAccept">

            <div class="row items-center q-col-gutter-sm no-wrap">
              <div class="col-shrink s-font-xxs s-font-md-xs">
                Для улучшения работы сайта и его взаимодействия с пользователями мы используем файлы cookie. Продолжая
                работу с сайтом, Вы разрешаете использование cookie-файлов. Вы всегда можете отключить файлы cookie в
                настройках Вашего браузера.
              </div>
              <div class="col-md-auto ">
                <q-btn class="gt-md" color="primary" outline @click="onCookieAccept">Принять</q-btn>

                <q-btn class="lt-md" color="primary" dense outline size="md" @click="onCookieAccept">Принять</q-btn>

              </div>
            </div>


          </q-card-section>

        </q-card>
      </q-dialog>

      <div class="footer q-py-lg">

        <div class="container">

          <div class="row q-col-gutter-lg">

            <div class="col-24 col-lg-14">

              <div class="row q-col-gutter-md  text-center">

                <div class="col-24 col-sm-12 col-md-8 c-menu">

                  <div class="c-menu-header">ИЗДАНИЕ</div>

                  <ul class="c-menu-items">
                    <li class="">
                      <router-link to="/page/info-about">
                        <span class="">О газете</span>
                      </router-link>
                    </li>
                    <li class="">
                      <router-link to="/page/info-about/podpiska">
                        <span class="">Подписка</span>
                      </router-link>
                    </li>
                    <li class="">
                      <router-link to="/page/advert/issue">
                        <span class="">Реклама в газете</span>
                      </router-link>
                    </li>
                    <li class="">
                      <router-link to="/advert/site/banner">
                        <span class="">Реклама на сайте</span>
                      </router-link>
                    </li>
                    <li class="">
                      <router-link to="/calendar">
                        <span class="">Календарь материалов</span>
                      </router-link>
                    </li>
                    <li class="">
                      <router-link to="/authors/">
                        <span class="">Авторы</span>
                      </router-link>
                    </li>
                    <li class="">
                      <router-link to="/tags/activity/">
                        <span class="">Теги</span>
                      </router-link>
                    </li>
                    <li class="">
                      <router-link to="/page/contact">
                        <span class="">Контакты</span>
                      </router-link>
                    </li>

                  </ul>

                </div>

                <div class="col-24 col-sm-12 col-md-8 c-menu">

                  <div class="c-menu-header">ОФИЦИАЛЬНО</div>

                  <ul class="c-menu-items">
                    <li class="">
                      <a href="https://www.ogirk.ru/pravo/reestr"><span class="">Правовой портал</span>
                      </a>
                    </li>
                    <li class="">
                      <a href="/group-official/"><span
                          class="">Информация о деятельности ОГАУ «Медиа-центр «Приангарье»</span>
                      </a>
                    </li>

                    <li class="">
                      <a href="/anti-corruption/laws/"><span class="">Противодействие коррупции</span>
                      </a>
                    </li>
                    <li class="">
                      <a href="/ohrana-truda/"><span class="">Охрана труда и техника безопасности</span></a>
                    </li>
                    <li class="">
                      <a href="/zakupki/"><span class="">Закупочная деятельность</span></a>
                    </li>
                  </ul>

                </div>

                <div class="col-24 col-sm-12 col-md-8 c-menu">

                  <div class="c-menu-header">СОЦИАЛЬНЫЕ СЕТИ</div>


                  <ul class="c-menu-items">
                    <li class="">
                      <a href="https://t.me/ogirk">
                        <q-icon :name="$icons.fabTelegramPlane" class="q-mr-sm"/>
                        <span class="">Telegram</span>
                      </a>
                    </li>
                    <li class="">
                      <a href="https://vk.com/ogirk">
                        <q-icon :name="$icons.fabVk" class="q-mr-sm"/>
                        <span class="">Vkontakte</span>
                      </a>
                    </li>
                    <li class="">
                      <a href="https://ok.ru/ogirk">
                        <q-icon :name="$icons.fabOdnoklassnikiSquare" class="q-mr-sm"/>
                        <span class="">Одноклассники</span>
                      </a>
                    </li>
                    <li class="">
                      <a href="https://www.youtube.com/channel/UC5M9401ITV-nkfPMM75VUxQ?view_as=subscriber">
                        <q-icon :name="$icons.fabYoutube" class="q-mr-sm"/>
                        <span class="">Youtube</span>
                      </a>
                    </li>

                  </ul>

                </div>

              </div>

            </div>

            <div class="col-24 col-lg-10 c-info">

              <h3 class="c-info-copyright q-ma-none q-mb-md">Официальный интернет-портал правовой информации Иркутской
                области</h3>

              <div class="c-info-desc">
                <p>
                  Официальный интернет-портал правовой информации Иркутской области. Зарегистрировано Федеральной
                  Службой по надзору в сфере связи, информационных технологий и массовых коммуникаций, серия ЭЛ
                  №ФС77-73560 от 24 августа 2018 года. Учредитель: областное государственное автономное учреждение
                  "Медиа-центр "Приангарье".
                </p>
                <p>
                  Главный редактор: Люстрицкий Дмитрий Георгиевич.
                </p>
                <p>
                  Сайт содержит материалы, охраняемые авторским правом, и средства индивидуализации (логотипы, фирменные
                  знаки). Любое использование информации, размещенной на сайте, допускается только при указании
                  гиперссылки на www.ogirk.ru как источник. К нарушителям применяются все меры, предусмотренные ст. 1301
                  ГК РФ.
                </p>
                <p>
                  Возрастное ограничение: 16+
                </p>
                <p>
                  Мнение авторов может не совпадать с позицией редакции.
                </p>

                <p>
                  На сайте используются фото с <a href="https://pixabay.com/ru/">pixabay.com</a>.
                </p>
              </div>

              <div class="c-info-link q-mb-sm">
                Телефон редакции: (3952) 200-628, 200-638
              </div>
              <div class="c-info-link q-mb-sm">
                Факс редакции: (3952) 200-674
              </div>

              <div class="c-info-link q-mb-sm">
                Адрес электронной почты редакции сайта: <a href="mailto:site@ogirk.ru">site@ogirk.ru</a>
              </div>

              <h3 class="c-info-copyright q-ma-none q-mb-md">© 2023 ОГАУ «Медиа-центр «Приангарье»</h3>

            </div>

          </div>

        </div>

      </div>

    </div>

  </div>

</template>

<script>


export default {
  name: 'layout.footer',
  serverCacheKey: props => 'layout.footer',
  components: {},
  apollo: {},
  data() {
    return {
      issuePrints: null,
      cookieDialog: false
    }
  },
  mounted() {

    this.cookieDialog = !this.$q.cookies.get('cookies_consent')

  },
  methods: {
    onCookieAccept() {

      this.$q.cookies.set('cookies_consent', 1, {path: '/', expires: 3000})
      this.cookieDialog = false

    }
  }
}
</script>

<style lang="scss" scoped>

.footer {
  background: #252525;
  color: #FFF;

  a {
    color: #FFF;

  }
}

.c-info-copyright {
  font-size: 19px;
  line-height: 1;
}

.c-info-desc {
  color: rgba(255, 255, 255, .72);
  font-size: 13px;
}

.c-info-link {

}

.c-menu {

  .c-menu-header {
    color: rgba(255, 255, 255, .5);
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin: 0 0 10px 0;
  }

  .c-menu-items {
    padding: 0;
    margin: 0;
    list-style-type: none;

    li {
      font-size: 17px;
      margin: 0 0 10px 0;

      a {
        text-decoration: none;
      }
    }
  }
}


</style>
