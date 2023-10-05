import { getLoggerBuilder } from '@nextcloud/logger'

export default getLoggerBuilder()
	.setApp('nmcsettings')
	.detectUser()
	.build()
