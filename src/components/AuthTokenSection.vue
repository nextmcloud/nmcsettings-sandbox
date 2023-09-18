<template>
	<div id="security" class="section">
		<h2>{{ t('nmcsettings', 'Devices & sessions', {}, undefined, {sanitize: false}) }}</h2>
		<AuthTokenList :tokens="tokens"
			@toggle-scope="toggleTokenScope"
			@rename="rename"
			@delete="deleteToken"
			@wipe="wipeToken" />
		<p class="settings-hint hidden-when-empty">
			{{ t('nmcsettings', 'You can terminate individual sessions here or remove them completely. When terminating, a new login is necessary. If you delete a session, all data of your MagentaCLOUD on the respective device will also be removed.') }}
		</p>
		<p class="settings-hint second hidden-when-empty">
			{{ t('nmcsettings', 'You can manually create a new session here and connect a new device to your MagentaCLOUD via login data or QR code.') }}
		</p>
		<AuthTokenSetupDialogue v-if="canCreateToken" :add="addNewToken" />
	</div>
</template>

<script>
import axios from '@nextcloud/axios'
import { confirmPassword } from '@nextcloud/password-confirmation' // eslint-disable-line n/no-unpublished-import
import '@nextcloud/password-confirmation/dist/style.css' // eslint-disable-line n/no-unpublished-import
import { generateUrl } from '@nextcloud/router'

import AuthTokenList from './AuthTokenList.vue'
import AuthTokenSetupDialogue from './AuthTokenSetupDialogue.vue'

const confirm = () => {
	return new Promise(resolve => {
		OC.dialogs.confirm(
			t('nmcsettings', 'Do you really want to wipe your data from this device?'),
			t('nmcsettings', 'Confirm wipe'),
			resolve,
			true,
		)
	})
}

/**
 * Tap into a promise without losing the value
 *
 * @param {Function} cb the callback
 * @return {any} val the value
 */
const tap = cb => val => {
	cb(val)
	return val
}

export default {
	name: 'AuthTokenSection',
	components: {
		AuthTokenSetupDialogue,
		AuthTokenList,
	},
	props: {
		tokens: {
			type: Array,
			required: true,
		},
		canCreateToken: {
			type: Boolean,
			required: true,
		},
	},
	data() {
		return {
			baseUrl: generateUrl('/settings/personal/authtokens'),
		}
	},
	methods: {
		addNewToken(name) {
			console.debug('creating a new app token', name)

			const data = {
				name,
			}
			return axios.post(this.baseUrl, data)
				.then(resp => resp.data)
				.then(tap(() => console.debug('app token created')))
				// eslint-disable-next-line vue/no-mutating-props
				.then(tap(data => this.tokens.push(data.deviceToken)))
				.catch(err => {
					console.error.bind('could not create app password', err)
					OC.Notification.showTemporary(t('nmcsettings', 'Error while creating device token'))
					throw err
				})
		},
		toggleTokenScope(token, scope, value) {
			console.debug('updating app token scope', token.id, scope, value)

			const oldVal = token.scope[scope]
			token.scope[scope] = value

			return this.updateToken(token)
				.then(tap(() => console.debug('app token scope updated')))
				.catch(err => {
					console.error.bind('could not update app token scope', err)
					OC.Notification.showTemporary(t('nmcsettings', 'Error while updating device token scope'))

					// Restore
					token.scope[scope] = oldVal

					throw err
				})
		},
		rename(token, newName) {
			console.debug('renaming app token', token.id, token.name, newName)

			const oldName = token.name
			token.name = newName

			return this.updateToken(token)
				.then(tap(() => console.debug('app token name updated')))
				.catch(err => {
					console.error.bind('could not update app token name', err)
					OC.Notification.showTemporary(t('nmcsettings', 'Error while updating device token name'))

					// Restore
					token.name = oldName
				})
		},
		updateToken(token) {
			return axios.put(this.baseUrl + '/' + token.id, token)
				.then(resp => resp.data)
		},
		deleteToken(token) {
			console.debug('deleting app token', token)

			// eslint-disable-next-line vue/no-mutating-props
			this.tokens = this.tokens.filter(t => t !== token)

			return axios.delete(this.baseUrl + '/' + token.id)
				.then(resp => resp.data)
				.then(tap(() => console.debug('app token deleted')))
				.catch(err => {
					console.error.bind('could not delete app token', err)
					OC.Notification.showTemporary(t('nmcsettings', 'Error while deleting the token'))

					// Restore
					// eslint-disable-next-line vue/no-mutating-props
					this.tokens.push(token)
				})
		},
		async wipeToken(token) {
			console.debug('wiping app token', token)

			try {
				await confirmPassword()

				if (!(await confirm())) {
					console.debug('wipe aborted by user')
					return
				}
				await axios.post(this.baseUrl + '/wipe/' + token.id)
				console.debug('app token marked for wipe')

				token.type = 2
			} catch (err) {
				console.error('could not wipe app token', err)
				OC.Notification.showTemporary(t('nmcsettings', 'Error while wiping the device with the token'))
			}
		},
	},
}
</script>

<style scoped>

</style>
