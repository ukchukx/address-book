/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

Vue.component('contacts', require('./components/Contacts.vue').default);
Vue.component('contact', require('./components/Contact.vue').default);

new Vue({ el: '#app' });
