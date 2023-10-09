window.addEventListener('DOMContentLoaded', function() {

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
