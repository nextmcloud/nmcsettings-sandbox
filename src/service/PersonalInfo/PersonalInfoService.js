import axios from '@nextcloud/axios'
import { getCurrentUser } from '@nextcloud/auth'
import { generateOcsUrl } from '@nextcloud/router'
import { confirmPassword } from '@nextcloud/password-confirmation'
import '@nextcloud/password-confirmation/dist/style.css'

/**
 * Save the primary account property value for the user
 *
 * @param {string} accountProperty the account property
 * @param {string|boolean} value the primary value
 * @return {object}
 */
export const savePrimaryAccountProperty = async (accountProperty, value) => {
	// TODO allow boolean values on backend route handler
	// Convert boolean to string for compatibility
	if (typeof value === 'boolean') {
		value = value ? '1' : '0'
	}

	const userId = getCurrentUser().uid
	const url = generateOcsUrl('cloud/users/{userId}', { userId })

	await confirmPassword()

	const res = await axios.put(url, {
		key: accountProperty,
		value,
	})

	return res.data
}
