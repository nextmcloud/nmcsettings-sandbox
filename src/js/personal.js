import Vue from 'vue'

import EmptySettingsContent from '../components/EmptySettingsContent.vue'

__webpack_nonce__ = btoa(OC.requestToken) // eslint-disable-line

const EmptySettingsView = Vue.extend(EmptySettingsContent)
new EmptySettingsView({
	propsData: {
		url: OC.generateUrl('/settings/user/account'),
	},
}).$mount('#app-content')
