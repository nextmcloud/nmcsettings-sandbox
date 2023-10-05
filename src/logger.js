import { getLoggerBuilder } from '@nextcloud/logger'

export default getLoggerBuilder()
	.setApp('settings')
	.detectUser()
	.build()
