/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import './bootstrap';
import { createApp } from 'vue';

import moment from 'moment';
window.moment = moment
// Vuetify
import '@mdi/font/css/materialdesignicons.css'

import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import { aliases, mdi } from 'vuetify/iconsets/mdi'

import { VDatePicker } from 'vuetify/labs/VDatePicker'

const vuetify = createVuetify({
  components:{
    VDatePicker,
  },
  directives,
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },

})

/**
 * Next, we will create a fresh Vue application instance. You may then begin
 * registering components with the application instance so they are ready
 * to use in your application's views. An example is included for you.
 */

const app = createApp().use(vuetify);

import ExampleComponent from './components/ExampleComponent.vue';
import ImportComponent from './components/import.vue';

import IndexComponent from './pages/index.vue';
import StatisticIndexComponent from './pages/statistic/index.vue';
import PeopleIndexComponent from './pages/people/index.vue';
import PeopleShowComponent from './pages/people/show.vue';

import ChartComponent from './components/chart.vue';

app.component('chart-component', ChartComponent);

app.component('index-component', IndexComponent);
app.component('example-component', ExampleComponent);

app.component('statistic-index-component', StatisticIndexComponent);
app.component('import-component', ImportComponent);
app.component('people-index-component', PeopleIndexComponent);
app.component('people-show-component', PeopleShowComponent);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// Object.entries(import.meta.glob('./**/*.vue', { eager: true })).forEach(([path, definition]) => {
//     app.component(path.split('/').pop().replace(/\.\w+$/, ''), definition.default);
// });

/**
 * Finally, we will attach the application instance to a HTML element with
 * an "id" attribute of "app". This element is included with the "auth"
 * scaffolding. Otherwise, you will need to add an element yourself.
 */

app.mount('#app');
