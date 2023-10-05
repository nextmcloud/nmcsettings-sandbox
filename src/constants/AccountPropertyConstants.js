import { translate as t } from '@nextcloud/l10n'

/** Enum of account properties */
export const ACCOUNT_PROPERTY_ENUM = Object.freeze({
	EMAIL_COLLECTION: 'additional_mail',
	EMAIL: 'email',
})

/** Enum of account properties to human readable account property names */
export const ACCOUNT_PROPERTY_READABLE_ENUM = Object.freeze({
	EMAIL_COLLECTION: t('nmcsettings', 'Additional email'),
	EMAIL: t('nmcsettings', 'Email'),
})

export const NAME_READABLE_ENUM = Object.freeze({
	[ACCOUNT_PROPERTY_ENUM.EMAIL]: ACCOUNT_PROPERTY_READABLE_ENUM.EMAIL,
})

/**
 * Enum of account setting properties
 *
 * Account setting properties unlike account properties do not support scopes*
 */
export const ACCOUNT_SETTING_PROPERTY_ENUM = Object.freeze({
	LANGUAGE: 'language',
	LOCALE: 'locale',
})

/** Enum of account setting properties to human readable setting properties */
export const ACCOUNT_SETTING_PROPERTY_READABLE_ENUM = Object.freeze({
	LANGUAGE: t('nmcsettings', 'Language'),
	LOCALE: t('nmcsettings', 'Locale'),
})

/** Enum of scopes */
export const SCOPE_ENUM = Object.freeze({
	PRIVATE: 'v2-private',
	LOCAL: 'v2-local',
	FEDERATED: 'v2-federated',
	PUBLISHED: 'v2-published',
})

/** Scope suffix */
export const SCOPE_SUFFIX = 'Scope'

/** Default additional email scope */
export const DEFAULT_ADDITIONAL_EMAIL_SCOPE = SCOPE_ENUM.LOCAL

/** Enum of verification constants, according to IAccountManager */
export const VERIFICATION_ENUM = Object.freeze({
	NOT_VERIFIED: 0,
	VERIFICATION_IN_PROGRESS: 1,
	VERIFIED: 2,
})

/**
 * Email validation regex
 *
 * Sourced from https://github.com/mpyw/FILTER_VALIDATE_EMAIL.js/blob/71e62ca48841d2246a1b531e7e84f5a01f15e615/src/regexp/ascii.ts*
 */
// eslint-disable-next-line no-control-regex
export const VALIDATE_EMAIL_REGEX = /^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-+[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-+[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/i
