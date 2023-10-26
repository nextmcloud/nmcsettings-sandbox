<template>
	<div class="language">
		<select :id="inputId"
			:placeholder="t('nmcsettings', 'Language')"
			@change="onLanguageChange">
			<option v-for="commonLanguage in commonLanguages"
				:key="commonLanguage.code"
				:selected="language.code === commonLanguage.code"
				:value="commonLanguage.code">
				{{ shortenLanguage(commonLanguage.name) }}
			</option>
		</select>
	</div>
</template>

<script>
import { ACCOUNT_SETTING_PROPERTY_ENUM } from '../../constants/AccountPropertyConstants.js'
import { savePrimaryAccountProperty } from '../../service/PersonalInfo/PersonalInfoService.js'
import { validateLanguage } from '../../utils/validate.js'
import { handleError } from '../../utils/handlers.js'

export default {
	name: 'Language',

	props: {
		inputId: {
			type: String,
			default: null,
		},
		commonLanguages: {
			type: Array,
			required: true,
		},
		language: {
			type: Object,
			required: true,
		},
	},

	data() {
		return {
			initialLanguage: this.language,
		}
	},

	computed: {
		allLanguages() {
			return Object.freeze(
				[...this.commonLanguages]
					.reduce((acc, { code, name }) => ({ ...acc, [code]: name }), {}),
			)
		},
	},

	methods: {
		async onLanguageChange(e) {
			const language = this.constructLanguage(e.target.value)
			this.$emit('update:language', language)

			if (validateLanguage(language)) {
				await this.updateLanguage(language)
				await this.updateLocale(language)
				this.reloadPage()
			}
		},

		async updateLanguage(language) {
			try {
				const responseData = await savePrimaryAccountProperty(ACCOUNT_SETTING_PROPERTY_ENUM.LANGUAGE, language.code)
				this.handleResponse({
					language,
					status: responseData.ocs?.meta?.status,
				})
			} catch (e) {
				this.handleResponse({
					errorMessage: t('nmcsettings', 'Unable to update language'),
					error: e,
				})
			}
		},

		async updateLocale(locale) {
			try {
				const responseDataLocale = await savePrimaryAccountProperty(ACCOUNT_SETTING_PROPERTY_ENUM.LOCALE, locale.code)
				this.handleResponse({
					locale,
					status: responseDataLocale.ocs?.meta?.status,
				})
			} catch (e) {
				this.handleResponse({
					errorMessage: t('nmcsettings', 'Unable to update language'),
					error: e,
				})
			}
		},

		constructLanguage(languageCode) {
			return {
				code: languageCode,
				name: this.allLanguages[languageCode],
			}
		},

		shortenLanguage(languageName) {
			const language = languageName.split(' ')
			return t('nmcsettings', language[0])
		},

		handleResponse({ language, status, errorMessage, error }) {
			if (status === 'ok') {
				// Ensure that local state reflects server state
				this.initialLanguage = language
			} else {
				handleError(error, errorMessage)
			}
		},

		reloadPage() {
			location.reload()
		},
	},
}
</script>

<style lang="scss" scoped>
.language {
	display: grid;

	select {
		width: 100%;
	}

	a {
		color: var(--color-main-text);
		text-decoration: none;
		width: max-content;
	}
}
</style>
