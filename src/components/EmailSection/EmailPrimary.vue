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
				disabled="disabled">

			<div class="email__actions-container">
				<transition name="fade">
					<Check v-if="showCheckmarkIcon" :size="20" />
					<AlertOctagon v-else-if="showErrorIcon" :size="20" />
				</transition>

				<NcActions class="email__actions"
					:aria-label="t('settings', 'Email options')"
					:force-menu="true">
					<NcActionButton v-if="!isNotificationEmail"
						:aria-label="setNotificationMailLabel"
						:close-after-click="true"
						:disabled="setNotificationMailDisabled"
						icon="icon-favorite"
						@click.stop.prevent="setNotificationMail">
						{{ setNotificationMailLabel }}
					</NcActionButton>
				</NcActions>
			</div>
		</div>

		<em v-if="isNotificationEmail">
			{{ t('settings', 'Primary email for password reset and notifications') }}
		</em>
	</div>
</template>

<script>
import { NcActions, NcActionButton } from '@nextcloud/vue'
import AlertOctagon from 'vue-material-design-icons/AlertOctagon.vue'
import Check from 'vue-material-design-icons/Check.vue'

import { handleError } from '../../utils/handlers.js'
import { ACCOUNT_PROPERTY_READABLE_ENUM, VERIFICATION_ENUM } from '../../constants/AccountPropertyConstants.js'
import { saveNotificationEmail } from '../../service/PersonalInfo/EmailService.js'

export default {
	name: 'EmailPrimary',

	components: {
		NcActions,
		NcActionButton,
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

		setNotificationMailDisabled() {
			return !this.primary && this.localVerificationState !== VERIFICATION_ENUM.VERIFIED
		},

		setNotificationMailLabel() {
			if (this.isNotificationEmail) {
				return t('settings', 'Unset as primary email')
			} else if (!this.primary && this.localVerificationState !== VERIFICATION_ENUM.VERIFIED) {
				return t('settings', 'This address is not confirmed')
			}
			return t('settings', 'Set as primary email')
		},

		inputId() {
			if (this.primary) {
				return 'email'
			}
			return `email-${this.index}`
		},

		inputPlaceholder() {
			if (this.primary) {
				return t('settings', 'Your email address')
			}
			return t('settings', 'Additional email address {index}', { index: this.index + 1 })
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
