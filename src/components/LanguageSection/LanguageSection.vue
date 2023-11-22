<template>
	<section>
		<div class="headerbar-label">
			<label :for="inputId" class="language-label">{{ propertyReadable }}</label>
		</div>

		<template v-if="isEditable">
			<Language :input-id="inputId"
				:common-languages="commonLanguages"
				:other-languages="otherLanguages"
				:language.sync="language" />
		</template>

		<span v-else>
			{{ t('nmcsettings', 'No language set') }}
		</span>
	</section>
</template>

<script>
import { loadState } from '@nextcloud/initial-state'

import Language from './Language.vue'

import { ACCOUNT_SETTING_PROPERTY_ENUM, ACCOUNT_SETTING_PROPERTY_READABLE_ENUM } from '../../constants/AccountPropertyConstants.js'

const { languageMap: { activeLanguage, commonLanguages, otherLanguages } } = loadState('settings', 'personalInfoParameters', {})

export default {
	name: 'LanguageSection',

	components: {
		Language,
	},

	data() {
		return {
			propertyReadable: ACCOUNT_SETTING_PROPERTY_READABLE_ENUM.LANGUAGE,
			commonLanguages,
			otherLanguages,
			language: activeLanguage,
		}
	},

	computed: {
		inputId() {
			return `account-setting-${ACCOUNT_SETTING_PROPERTY_ENUM.LANGUAGE}`
		},

		isEditable() {
			return Boolean(this.language)
		},
	},
}
</script>

<style lang="scss" scoped>
section {
	padding: 10px 10px;

	&::v-deep button:disabled {
		cursor: default;
	}
}
</style>
