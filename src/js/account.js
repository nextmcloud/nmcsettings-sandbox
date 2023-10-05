import Vue from 'vue'
import { getRequestToken } from '@nextcloud/auth'
import { translate as t } from '@nextcloud/l10n'
import '@nextcloud/dialogs/dist/index.css'

import EmailSection from '../components/EmailSection/EmailSection.vue'
import LanguageSection from '../components/LanguageSection/LanguageSection.vue'

__webpack_nonce__ = btoa(getRequestToken()) // eslint-disable-line

Vue.mixin({
	methods: {
		t,
	},
})

const EmailView = Vue.extend(EmailSection)
const LanguageView = Vue.extend(LanguageSection)

new EmailView().$mount('#vue-email-section')
new LanguageView().$mount('#vue-language-section')
