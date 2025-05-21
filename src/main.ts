import {createApp} from 'vue'
import './style.css'
import App from './App.vue'
import moment from "moment-timezone";

moment.tz.setDefault('UTC');
createApp(App).mount('#acethetest-dashboard')
