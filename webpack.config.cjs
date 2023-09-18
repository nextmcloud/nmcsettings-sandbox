// webpack with standard nextcloud config 
const path = require('path')
const webpack = require('webpack')
const webpackConfig = require('@nextcloud/webpack-vue-config')

webpackConfig.entry = {
	    ...webpackConfig.entry, 
		nmcsettings: path.join(__dirname, 'src', 'js', 'nmcsettings.js'),
		personal: path.join(__dirname, 'src', 'js', 'personal.js'),
		sessions: path.join(__dirname, 'src', 'js', 'sessions.js'),
	}

// Workaround for https://github.com/nextcloud/webpack-vue-config/pull/432 causing problems with nextcloud-vue-collections
webpackConfig.resolve.alias = {}

webpackConfig.resolve.extensions = ['.*', '.js', '.ts', '.vue', '.json']

module.exports = webpackConfig
