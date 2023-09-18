<template>
	<div v-if="!adding" class="row spacing">
		<!-- Port to TextField component when available -->
		<input v-model="deviceName"
			type="text"
			:maxlength="120"
			:disabled="loading"
			:placeholder="t('nmcsettings', 'Session name')"
			@keydown.enter="submit">
		<NcButton :disabled="loading || deviceName.length === 0"
			type="primary"
			@click="submit">
			{{ t('nmcsettings', 'Create new session') }}
		</NcButton>
	</div>
	<div v-else class="spacing">
		{{ t('nmcsettings', 'Use the credentials below to configure your app or device.') }}
		{{ t('nmcsettings', 'For security reasons this password will only be shown once.') }}
		<div class="app-password-row">
			<label for="app-username" class="app-password-label">{{ t('nmcsettings', 'Username') }}</label>
			<input id="app-username"
				:value="loginName"
				type="text"
				class="monospaced"
				readonly="readonly"
				@focus="selectInput">
		</div>
		<div class="app-password-row">
			<label for="app-password" class="app-password-label">{{ t('nmcsettings', 'Password') }}</label>
			<input id="app-password"
				ref="appPassword"
				:value="appPassword"
				type="text"
				class="monospaced"
				readonly="readonly"
				@focus="selectInput">
			<NcButton type="tertiary"
				:title="copyTooltipOptions"
				:aria-label="copyTooltipOptions"
				@click="copyPassword">
				<template #icon>
					<Check v-if="copied" :size="20" />
					<ContentCopy v-else :size="20" />
				</template>
			</NcButton>
		</div>
		<div class="app-password-row">
			<div class="row-left">
				<span class="app-password-label" />
				<a v-if="!showQR"
					@click="showQR = true">
					{{ t('nmcsettings', 'Show QR code') }}
				</a>
				<QR v-else
					:value="qrUrl" />
			</div>
			<div class="row-right">
				<NcButton @click="reset">
					{{ t('nmcsettings', 'Done') }}
				</NcButton>
			</div>
		</div>
	</div>
</template>

<script>
import QR from '@chenfengyuan/vue-qrcode' // eslint-disable-line n/no-unpublished-import
import { confirmPassword } from '@nextcloud/password-confirmation' // eslint-disable-line n/no-unpublished-import
import '@nextcloud/password-confirmation/dist/style.css' // eslint-disable-line n/no-unpublished-import
import { showError } from '@nextcloud/dialogs' // eslint-disable-line n/no-extraneous-import
import { getRootUrl } from '@nextcloud/router'
import NcButton from '@nextcloud/vue/dist/Components/NcButton' // eslint-disable-line n/no-missing-import

import Check from 'vue-material-design-icons/Check.vue' // eslint-disable-line n/no-extraneous-import
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue' // eslint-disable-line n/no-extraneous-import

export default {
	name: 'AuthTokenSetupDialogue',
	components: {
		Check,
		ContentCopy,
		NcButton,
		QR,
	},
	props: {
		add: {
			type: Function,
			required: true,
		},
	},
	data() {
		return {
			adding: false,
			loading: false,
			deviceName: '',
			appPassword: '',
			loginName: '',
			copied: false,
			showQR: false,
			qrUrl: '',
		}
	},
	computed: {
		copyTooltipOptions() {
			if (this.copied) {
				return t('nmcsettings', 'Copied!')
			}
			return t('nmcsettings', 'Copy Password')
		},
	},
	methods: {
		selectInput(e) {
			e.currentTarget.select()
		},
		submit() {
			confirmPassword()
				.then(() => {
					this.loading = true
					return this.add(this.deviceName)
				})
				.then(token => {
					this.adding = true
					this.loginName = token.loginName
					this.appPassword = token.token

					const server = window.location.protocol + '//' + window.location.host + getRootUrl()
					this.qrUrl = `nc://login/user:${token.loginName}&password:${token.token}&server:${server}`

					this.$nextTick(() => {
						this.$refs.appPassword.select()
					})
				})
				.catch(err => {
					console.error('could not create a new app password', err)
					OC.Notification.showTemporary(t('nmcsettings', 'Error while creating device token'))

					this.reset()
				})
		},
		async copyPassword() {
			try {
				await navigator.clipboard.writeText(this.appPassword)
				this.copied = true
			} catch (e) {
				this.copied = false
				console.error(e)
				showError(t('nmcsettings', 'Could not copy app password. Please copy it manually.'))
			} finally {
				setTimeout(() => {
					this.copied = false
				}, 4000)
			}
		},
		reset() {
			this.adding = false
			this.loading = false
			this.showQR = false
			this.qrUrl = ''
			this.deviceName = ''
			this.appPassword = ''
			this.loginName = ''
		},
	},
}
</script>

<style lang="scss" scoped>
	.app-password-row {
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

		&:last-child {
			align-items: normal;
		}

		.row-left {
			padding-left: 100px;
			width: 280px;
		}

		.row-right {
			display: inline-flex;
			justify-content: end;
			width: 220px;
		}

		canvas {
			margin-left: -14px;
			margin-top: -14px;
		}
	}

	.app-password-label {
		display: table-cell;
		padding-right: 1em;
		text-align: right;
		vertical-align: middle;
		width: 100px;
	}

	.row input {
		height: 44px !important;
		padding: 7px 12px;
		margin-right: 12px;
		width: 200px;
	}

	.monospaced {
		width: 245px;
		font-family: monospace;
	}

	.button-vue{
		display:inline-block;
		margin: 3px 3px 3px 3px;
	}

	.row {
		display: flex;
		align-items: center;
	}

	.spacing {
		padding-top: 16px;
	}
</style>
