import Vue from 'vue'
import UserMenu from '../components/UserMenu.vue'
import { translate as t } from '@nextcloud/l10n'

const app = 'nmcsettings'

const menuItems = [{
	id: 'settings',
	name: t(app, 'Account Settings'),
	url: OC.generateUrl('/settings/user/account'),
	target: '_self',
}, {
	id: 'help',
	name: t(app, 'Help & FAQ'),
	url: 'https://cloud.telekom-dienste.de/hilfe',
	target: '_blank',
}, {
	id: 'customer_center',
	name: t(app, 'Customer Center'),
	url: 'https://www.telekom.de/mein-kundencenter',
	target: '_blank',
}]

window.addEventListener('DOMContentLoaded', function() {

	document.querySelectorAll('nav.user-menu__nav li').forEach(function(element) {
		const core = 'core_'
		const admin = 'admin_'

		if (element.id !== 'logout' && !element.id.includes(core) && !element.id.includes(admin)) {
			element.remove()
		}
	})

	const head = document.querySelector('head')
	const user = head.attributes['data-user-displayname'].value

	const menuButton = document.querySelector('#user-menu > a')
	menuButton.innerHTML = '<span>' + user + '</span>'

	const searchButton = document.querySelector('#unified-search > a')
	searchButton.innerHTML = '<span>' + t(app, 'Search') + '</span>'

	const menuElements = document.createElement('div')
	menuElements.id = 'nmcsettings-menu'
	const userMenu = document.querySelector('nav.user-menu__nav ul')
	userMenu.prepend(menuElements)
	const View = Vue.extend(UserMenu)
	new View({ propsData: { menuItems } }).$mount('#nmcsettings-menu')

	const settingsBody = document.getElementById('body-settings')
	if (!settingsBody) return

	// removes app navigation elements
	document.querySelectorAll('[data-section-type]').forEach(function(element) {
		const type = element.attributes['data-section-type'].value
		if (type === 'personal') {
			const id = element.attributes['data-section-id'].value
			if (id !== 'account' && id !== 'sessions') {
				element.remove()
			}
		}
	})

	// shows hidden app navigation elements in NC25
	document.querySelectorAll('#app-navigation li a').forEach(function(element) {
		const href = element.href
		const admin = '/admin'
		const account = '/account'
		const sessions = '/sessions'

		if (href.includes(admin) || href.includes(account) || href.includes(sessions)) {
			if (href.includes(admin)) {
				document.querySelectorAll('.app-navigation-caption').forEach(function(caption) {
					caption.style.display = 'flex'
				})
			}
			element.parentElement.style.display = 'flex'
		}
	})
})
