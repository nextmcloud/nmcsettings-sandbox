import { VALIDATE_EMAIL_REGEX } from '../constants/AccountPropertyConstants.js'

/**
 * Validate the email input
 *
 * Compliant with PHP core FILTER_VALIDATE_EMAIL validator*
 *
 * Reference implementation https://github.com/mpyw/FILTER_VALIDATE_EMAIL.js/blob/71e62ca48841d2246a1b531e7e84f5a01f15e615/src/index.ts*
 *
 * @param {string} input the input
 * @return {boolean}
 */
export function validateEmail(input) {
	return typeof input === 'string'
		&& VALIDATE_EMAIL_REGEX.test(input)
		&& input.slice(-1) !== '\n'
		&& input.length <= 320
		&& encodeURIComponent(input).replace(/%../g, 'x').length <= 320
}

/**
 * Validate the language input
 *
 * @param {object} input the input
 * @return {boolean}
 */
export function validateLanguage(input) {
	return input.code !== ''
		&& input.name !== ''
		&& input.name !== undefined
}
