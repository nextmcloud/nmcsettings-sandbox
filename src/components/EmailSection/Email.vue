<template>
	<div>
		<div class="email">
			<input :id="inputId"
				ref="email"
				type="email"
				:placeholder="inputPlaceholder"
				:value="email"
				:aria-describedby="helperText ? `${inputId}-helper-text` : ''"
				autocapitalize="none"
				autocomplete="on"
				autocorrect="off"
				class="additional"
				@input="onEmailChange">

			<div class="email__actions-container">
				<transition name="fade">
					<Check v-if="showCheckmarkIcon" :size="20" />
					<AlertOctagon v-else-if="showErrorIcon" :size="20" />
				</transition>

				<NcActions class="email__actions"
					:aria-label="t('nmcsettings', 'Email options')"
					:force-menu="true">
					<NcActionButton v-if="!primary || !isNotificationEmail"
						:aria-label="setNotificationMailLabel"
						:close-after-click="true"
						:disabled="setNotificationMailDisabled"
						:icon="setNotificationMailIcon"
						@click.stop.prevent="setNotificationMail">
						{{ setNotificationMailLabel }}
					</NcActionButton>
					<NcActionButton :aria-label="deleteEmailLabel"
						:close-after-click="true"
						:disabled="deleteDisabled"
						icon="icon-delete"
						@click.stop.prevent="deleteEmail">
						{{ deleteEmailLabel }}
					</NcActionButton>
				</NcActions>
			</div>
		</div>

		<p v-if="helperText"
			:id="`${inputId}-helper-text`"
			class="email__helper-text-message email__helper-text-message--error">
			<AlertCircle class="email__helper-text-message__icon" :size="18" />
			{{ helperText }}
		</p>

		<em v-if="isNotificationEmail">
			{{ t('nmcsettings', 'Primary email for password reset and notifications') }}
		</em>
	</div>
</template>

<script>
import { NcActions, NcActionButton } from '@nextcloud/vue'
import AlertCircle from 'vue-material-design-icons/AlertCircleOutline.vue'
import AlertOctagon from 'vue-material-design-icons/AlertOctagon.vue'
import Check from 'vue-material-design-icons/Check.vue'
import debounce from 'debounce'

import { handleError } from '../../utils/handlers.js'
import { ACCOUNT_PROPERTY_READABLE_ENUM, VERIFICATION_ENUM } from '../../constants/AccountPropertyConstants.js'
import {
	removeAdditionalEmail,
	saveAdditionalEmail,
	saveNotificationEmail,
	savePrimaryEmail,
	updateAdditionalEmail,
} from '../../service/PersonalInfo/EmailService.js'
import { validateEmail } from '../../utils/validate.js'

export default {
	name: 'Email',

	components: {
		NcActions,
		NcActionButton,
		AlertCircle,
		AlertOctagon,
		Check,
	},

	props: {
		email: {
			type: String,
			required: true,
		},
		index: {
			type: Number,
			default: 0,
		},
		primary: {
			type: Boolean,
			default: false,
		},
		activeNotificationEmail: {
			type: String,
			default: '',
		},
		localVerificationState: {
			type: Number,
			default: VERIFICATION_ENUM.NOT_VERIFIED,
		},
	},

	data() {
		return {
			propertyReadable: ACCOUNT_PROPERTY_READABLE_ENUM.EMAIL,
			initialEmail: this.email,
			helperText: null,
			showCheckmarkIcon: false,
			showErrorIcon: false,
		}
	},

	computed: {
		deleteDisabled() {
			if (this.primary) {
				// Disable for empty primary email as there is nothing to delete
				// OR when initialEmail (reflects server state) and email (current input) are not the same
				return this.email === '' || this.initialEmail !== this.email
			} else if (this.initialEmail !== '') {
				return this.initialEmail !== this.email
			}
			return false
		},

		deleteEmailLabel() {
			if (this.primary) {
				return t('nmcsettings', 'Remove primary email')
			}
			return t('nmcsettings', 'Delete email')
		},

		setNotificationMailDisabled() {
			return !this.primary && this.localVerificationState !== VERIFICATION_ENUM.VERIFIED
		},

		setNotificationMailLabel() {
			if (this.isNotificationEmail) {
				return t('nmcsettings', 'Unset as primary email')
			} else if (!this.primary && this.localVerificationState !== VERIFICATION_ENUM.VERIFIED) {
				return t('nmcsettings', 'This address is not confirmed')
			}
			return t('nmcsettings', 'Set as primary email')
		},

		setNotificationMailIcon() {
			if (!this.primary && this.localVerificationState !== VERIFICATION_ENUM.VERIFIED) {
				return 'icon-mail-opened'
			}
			return 'icon-auto-login'
		},

		inputId() {
			if (this.primary) {
				return 'email'
			}
			return `email-${this.index}`
		},

		inputPlaceholder() {
			if (this.primary) {
				return t('nmcsettings', 'Your email address')
			}
			return t('nmcsettings', 'Additional email address {index}', { index: this.index + 1 })
		},

		isNotificationEmail() {
			return (this.email && this.email === this.activeNotificationEmail)
				|| (this.primary && this.activeNotificationEmail === '')
		},
	},

	mounted() {
		if (!this.primary && this.initialEmail === '') {
			// $nextTick is needed here, otherwise it may not always work https://stackoverflow.com/questions/51922767/autofocus-input-on-mount-vue-ios/63485725#63485725
			this.$nextTick(() => this.$refs.email?.focus())
		}
	},

	methods: {
		onEmailChange(e) {
			this.$emit('update:email', e.target.value)
			this.debounceEmailChange(e.target.value.trim())
		},

		debounceEmailChange: debounce(async function(email) {
			this.helperText = null
			if (this.$refs.email?.validationMessage) {
				this.helperText = this.$refs.email.validationMessage
				return
			}
			if (validateEmail(email) || email === '') {
				if (this.primary) {
					await this.updatePrimaryEmail(email)
				} else {
					if (email) {
						if (this.initialEmail === '') {
							await this.addAdditionalEmail(email)
						} else {
							await this.updateAdditionalEmail(email)
						}
					}
				}
			}
		}, 500),

		async deleteEmail() {
			if (this.primary) {
				this.$emit('update:email', '')
				await this.updatePrimaryEmail('')
			} else {
				await this.deleteAdditionalEmail()
			}
		},

		async updatePrimaryEmail(email) {
			try {
				const responseData = await savePrimaryEmail(email)
				this.handleResponse({
					email,
					status: responseData.ocs?.meta?.status,
				})
			} catch (e) {
				if (email === '') {
					this.handleResponse({
						errorMessage: t('nmcsettings', 'Unable to delete primary email address'),
						error: e,
					})
				} else {
					this.handleResponse({
						errorMessage: t('nmcsettings', 'Unable to update primary email address'),
						error: e,
					})
				}
			}
		},

		async addAdditionalEmail(email) {
			try {
				const responseData = await saveAdditionalEmail(email)
				this.handleResponse({
					email,
					status: responseData.ocs?.meta?.status,
				})
			} catch (e) {
				this.handleResponse({
					errorMessage: t('nmcsettings', 'Unable to add additional email address'),
					error: e,
				})
			}
		},

		async setNotificationMail() {
		  try {
			  const newNotificationMailValue = (this.primary || this.isNotificationEmail) ? '' : this.initialEmail
			  const responseData = await saveNotificationEmail(newNotificationMailValue)
			  this.handleResponse({
				  notificationEmail: newNotificationMailValue,
				  status: responseData.ocs?.meta?.status,
			  })
		  } catch (e) {
			  this.handleResponse({
				  errorMessage: 'Unable to choose this email for notifications',
				  error: e,
			  })
		  }
		},

		async updateAdditionalEmail(email) {
			try {
				const responseData = await updateAdditionalEmail(this.initialEmail, email)
				this.handleResponse({
					email,
					status: responseData.ocs?.meta?.status,
				})
			} catch (e) {
				this.handleResponse({
					errorMessage: t('nmcsettings', 'Unable to update additional email address'),
					error: e,
				})
			}
		},

		async deleteAdditionalEmail() {
			try {
				const responseData = await removeAdditionalEmail(this.initialEmail)
				this.handleDeleteAdditionalEmail(responseData.ocs?.meta?.status)
			} catch (e) {
				this.handleResponse({
					errorMessage: t('nmcsettings', 'Unable to delete additional email address'),
					error: e,
				})
			}
		},

		handleDeleteAdditionalEmail(status) {
			if (status === 'ok') {
				this.$emit('delete-additional-email')
			} else {
				this.handleResponse({
					errorMessage: t('nmcsettings', 'Unable to delete additional email address'),
				})
			}
		},

		handleResponse({ email, notificationEmail, status, errorMessage, error }) {
			if (status === 'ok') {
				// Ensure that local state reflects server state
				if (email) {
					this.initialEmail = email
				} else if (notificationEmail !== undefined) {
					this.$emit('update:notification-email', notificationEmail)
				}
				this.showCheckmarkIcon = true
				setTimeout(() => { this.showCheckmarkIcon = false }, 2000)
			} else {
				handleError(error, errorMessage)
				this.showErrorIcon = true
				setTimeout(() => { this.showErrorIcon = false }, 2000)
			}
		},
	},
}
</script>

<style lang="scss" scoped>
.email {
	display: grid;
	align-items: center;

	input {
		grid-area: 1 / 1;
		width: 100%;
	}

	.email__actions-container {
		grid-area: 1 / 1;
		justify-self: flex-end;
		height: 30px;

		display: flex;
		gap: 0 2px;
		margin-right: 5px;

		.email__actions {
			opacity: 0.4 !important;

			&:hover,
			&:focus,
			&:active {
				opacity: 0.8 !important;
			}

			&::v-deep button {
				height: 30px !important;
				min-height: 30px !important;
				width: 30px !important;
				min-width: 30px !important;
			}
		}
	}

	&__helper-text-message {
		padding: 4px 0;
		display: flex;
		align-items: center;

		&__icon {
			margin-right: 8px;
			align-self: start;
			margin-top: 4px;
		}

		&--error {
			color: var(--color-error);
		}
	}
}

.fade-enter,
.fade-leave-to {
	opacity: 0;
}

.fade-enter-active {
	transition: opacity 200ms ease-out;
}

.fade-leave-active {
	transition: opacity 300ms ease-out;
}
</style>
