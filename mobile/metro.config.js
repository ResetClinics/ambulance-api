// eslint-disable-next-line import/no-extraneous-dependencies
const { getDefaultConfig } = require('expo/metro-config')

module.exports = (async () => getDefaultConfig(__dirname))()
