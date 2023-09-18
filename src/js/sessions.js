import { loadState } from '@nextcloud/initial-state'
import Vue from 'vue'
import VTooltip from 'v-tooltip' // eslint-disable-line

import WebDavSection from '../components/WebDavSection.vue'
import AuthTokenSection from '../components/AuthTokenSection.vue'

__webpack_nonce__ = btoa(OC.requestToken) // eslint-disable-line

Vue.use(VTooltip, { defaultHtml: false })
Vue.prototype.t = t

const WebDavView = Vue.extend(WebDavSection)
new WebDavView({
	propsData: {
		url: 'https://magentacloud.de/remote.php/webdav',
	},
}).$mount('#security-webdav')

const AuthTokensView = Vue.extend(AuthTokenSection)
new AuthTokensView({
	propsData: {
		tokens: loadState('settings', 'app_tokens'),
		canCreateToken: loadState('settings', 'can_create_app_token'),
	},
}).$mount('#security-authtokens')
