import { loadState } from '@nextcloud/initial-state'
import Vue from 'vue'
import VTooltip from 'v-tooltip' // eslint-disable-line

import AuthTokenSection from '../components/AuthTokenSection.vue'

__webpack_nonce__ = btoa(OC.requestToken) // eslint-disable-line

Vue.use(VTooltip, { defaultHtml: false })
Vue.prototype.t = t

const AuthTokensView = Vue.extend(AuthTokenSection)
new AuthTokensView({
	propsData: {
		tokens: loadState('settings', 'app_tokens'),
		canCreateToken: loadState('settings', 'can_create_app_token'),
	},
}).$mount('#security-authtokens')
