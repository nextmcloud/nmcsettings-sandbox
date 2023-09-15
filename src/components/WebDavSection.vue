<template>
	<div id="webdav-address" class="section">
		<h2>{{ t('nmcsettings', 'WebDAV Address') }}</h2>
		<p class="settings-hint hidden-when-empty">
			{{ t('nmcsettings', 'With the WebDAV address, you can set up your MagentaCLOUD as a network drive on Windows, for example. You can find more information about WebDAV and how to use it') }}
			<a href="https://cloud.telekom-dienste.de/hilfe#einrichten"
				target="_blank"
				rel="noreferrer noopener">
				{{ t('nmcsettings', 'here.') }}
			</a>
		</p>
		<div class="webdav-row">
			<input id="webdav-url"
				ref="webDavUrl"
				:value="webDavUrl"
				type="text"
				class="monospaced"
				readonly="readonly"
				@focus="selectInput">
			<NcButton type="tertiary"
				:title="copyTooltipOptions"
				:aria-label="copyTooltipOptions"
				@click="copyUrl">
				<template #icon>
					<Check v-if="copied" :size="20" />
					<ContentCopy v-else :size="20" />
				</template>
			</NcButton>
		</div>
	</div>
</template>

<script>
import { showError } from '@nextcloud/dialogs' // eslint-disable-line n/no-extraneous-import
import NcButton from '@nextcloud/vue/dist/Components/NcButton' // eslint-disable-line n/no-missing-import
import Check from 'vue-material-design-icons/Check.vue' // eslint-disable-line n/no-extraneous-import
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue' // eslint-disable-line n/no-extraneous-import

export default {
	name: 'WebDavSection',
	components: {
		Check,
		ContentCopy,
		NcButton,
	},
	props: {
		url: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			webDavUrl: this.url,
			copied: false,
		}
	},
	computed: {
		copyTooltipOptions() {
			if (this.copied) {
				return t('nmcsettings', 'Copied!')
			}
			return t('nmcsettings', 'Copy')
		},
	},
	methods: {
		selectInput(e) {
			e.currentTarget.select()
		},
		async copyUrl() {
			try {
				await navigator.clipboard.writeText(this.url)
				this.copied = true
			} catch (e) {
				this.copied = false
				console.error(e)
				showError(t('nmcsettings', 'Could not copy WebDAV address. Please copy it manually.'))
			} finally {
				setTimeout(() => {
					this.copied = false
				}, 4000)
			}
		},
	},
}
</script>

<style lang="scss" scoped>
	.webdav-row {
		display: flex;
		align-items: center;

		.icon {
			background-size: 16px 16px;
			display: inline-block;
			position: relative;
			top: 3px;
			margin-left: 5px;
			margin-right: 8px;
		}
	}
</style>
