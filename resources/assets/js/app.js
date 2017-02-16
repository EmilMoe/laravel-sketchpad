import Vue          from 'vue'
import VueRouter    from 'vue-router'

import config       from './state/config'

import Run          from '../vue/content/Result.vue'
import Home         from '../vue/pages/Home.vue'
import Help         from '../vue/pages/Help.vue'
import Settings     from '../vue/pages/Settings.vue'

import App          from '../vue/App.vue'

Vue.use(VueRouter);

config();

window.router = new VueRouter({
	root: $('meta[name="route"]').attr('content'),
	history: true,
	saveScrollPosition: true
});

const routes = {
	// '/': {redirect: '/parent'},
	'/': { component: Home },
	'/run/*route': { component: Run, canReuse:false },
	'/settings' : { component: Settings },
	'/help': { component: Help },
	'*' : { component: Home}
};

router.map(routes);

router.start(App, '#app');
