const path = require('path');
const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {

	entry: {
		'js/react/addon': path.resolve(__dirname, '_src/react/addon.js'),
		'js/react/admin': path.resolve(__dirname, '_src/react/admin.js'),
		'js/react/comms': path.resolve(__dirname, '_src/react/comms.js'),
		'js/react/members': path.resolve(__dirname, '_src/react/members.js'),
		'js/react/memberships': path.resolve(__dirname, '_src/react/memberships.js'),
		'js/react/settings': path.resolve(__dirname, '_src/react/settings.js'),
		'js/react/transactions': path.resolve(__dirname, '_src/react/transactions.js'),
		'js/react/invites': path.resolve(__dirname, '_src/react/invites.js'),
		'js/react/coupons': path.resolve(__dirname, '_src/react/coupons.js')
	},

	output: {
		filename: '[name].js',
		path: path.resolve(__dirname, 'assets'),
	},

	optimization: {
		minimizer: [new TerserPlugin({ extractComments: false })],
	},

	resolve: {
		alias: {
			utils: path.resolve(__dirname, '_src/react/utils'),
			ui: path.resolve(__dirname, '_src/react/admin/ui'),
			layout: path.resolve(__dirname, '_src/react/admin/containers/layout')
		},
		extensions: [".js", ".jsx", ".json"],
	},

	module: {
		rules: [
			{
				test: /\.(js|jsx)$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader'
				}
			},
			{
				test: /\.css$/,
				use: [MiniCssExtractPlugin.loader,'style-loader', 'css-loader']
			}
		]
	},

	plugins : [new MiniCssExtractPlugin()]
}