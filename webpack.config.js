const path = require('path');
const webpack = require('webpack');
const TerserPlugin = require('terser-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const NodePolyfillPlugin = require("node-polyfill-webpack-plugin");

module.exports = {

	entry: {
		'js/react/addon': path.resolve(__dirname, '_src/react/addon.js'),
		'js/react/admin': path.resolve(__dirname, '_src/react/admin.js'),
		'js/react/comms': path.resolve(__dirname, '_src/react/comms.js'),
		'js/react/members': path.resolve(__dirname, '_src/react/members.js'),
		'js/react/memberships': path.resolve(__dirname, '_src/react/memberships.js'),
		'js/react/settings': path.resolve(__dirname, '_src/react/settings.js'),
		'js/react/transactions': path.resolve(__dirname, '_src/react/transactions.js'),
		'js/react/marketing': path.resolve(__dirname, '_src/react/marketing.js'),
		'js/react/invites': path.resolve(__dirname, '_src/react/invites.js'),
		'js/react/coupons': path.resolve(__dirname, '_src/react/coupons.js'),
		'js/react/rules': path.resolve(__dirname, '_src/react/rules.js'),
		'js/react/wizard': path.resolve(__dirname, '_src/react/wizard.js')
	},

	output: {
		filename: '[name].min.js',
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
		fallback : {
			"fs": false,
			"tls": false,
			"net": false,
			"path": false,
			"zlib": false,
			"http": false,
			"https": false,
			"stream": false,
			"crypto": false,
		}
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
				use: ['style-loader', 'css-loader']
			},
			{
				test: /\.less$/,
				use: [{
					loader: "style-loader"
				}, {
					loader: "css-loader"
				}, {
					loader: "less-loader",
					options: {
						lessOptions: {
							javascriptEnabled: true,
							modifyVars: { '@base-color': '#ffffff' }
						}
					}
				}]
			}
		]
	},

	plugins : [
		new MiniCssExtractPlugin(),
		new webpack.ProvidePlugin({
			process: 'process/browser',
	  	}), 
	  	new NodePolyfillPlugin()
	]
}