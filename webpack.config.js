const path = require('path');
const CompressionPlugin = require('compression-webpack-plugin');

module.exports = (env, argv) => {
	let production = argv.mode === 'production'

	return {
		entry: {
			'js/react/addon': path.resolve(__dirname, '_src/react/addon.js'),
			'js/react/admin': path.resolve(__dirname, '_src/react/admin.js'),
			'js/react/comms': path.resolve(__dirname, '_src/react/comms.js'),
			'js/react/members': path.resolve(__dirname, '_src/react/members.js'),
			'js/react/memberships': path.resolve(__dirname, '_src/react/memberships.js'),
			'js/react/settings': path.resolve(__dirname, '_src/react/settings.js'),
			'js/react/transactions': path.resolve(__dirname, '_src/react/transactions.js'),
			'js/react/invites': path.resolve(__dirname, '_src/react/invites.js'),
			'js/react/coupons': path.resolve(__dirname, '_src/react/coupons.js'),
			//'js/react/shortcode': path.resolve(__dirname, '_src/react/shortcode.js'),
			//'js/react/widget': path.resolve(__dirname, '_src/react/widget.js'),
		},

		output: {
			filename: '[name].js',
			path: path.resolve(__dirname, 'assets'),
		},

		plugins: [
			new CompressionPlugin({
				test: /\.js(\?.*)?$/i,
			}),
		],
		performance: {
			hints: "warning",
			// Calculates sizes of gziped bundles.
			assetFilter: function (assetFilename) {
				return assetFilename.endsWith(".js.gz");
			},
		},

		devtool: '',

		resolve: {
			extensions: [".js", ".jsx", ".json"],
		},

		module: {
			rules: [
				{
					test: /\.jsx?$/,
					exclude: /node_modules/,
					loader: 'babel-loader',
				},
			],
		},
	};
}
