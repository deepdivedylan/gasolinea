const HtmlWebpackPlugin = require('html-webpack-plugin');
const MiniCSSExtractPlugin = require('mini-css-extract-plugin');
const helpers = require('./helpers');
const webpack = require('webpack');

module.exports = {
	entry: {
		'vendor': helpers.root('webpack') + '/vendor.js',
		'app': helpers.root('webpack') + '/app.js',
		'css': helpers.root('webpack') + '/app.css'
	},

	mode: 'production',
	output: {
		path: helpers.root('public_html/dist'),
		publicPath: 'dist',
		filename: '[name].[hash].js',
		chunkFilename: '[id].[hash].chunk.js'
	},

	module: {
		rules: [
			{
				test: /\.(html|php)$/,
				loader: 'html-loader'
			},
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: ['@babel/preset-env']
					}
				}
			},
			{
				test: /\.css$/,
				loader: [MiniCSSExtractPlugin.loader, 'css-loader']
			}
		]
	},

	plugins: [
		new HtmlWebpackPlugin({
			inject: 'head',
			filename: helpers.root('public_html') + '/index.php',
			template: helpers.root('webpack') + '/index.php'
		}),
		new webpack.ProvidePlugin({
			$: 'jquery',
			jquery: 'jquery',
			'window.jquery': 'jquery',
			Cookies: 'js-cookie',
			Popper: ['popper.js', 'default']
		}),
		new MiniCSSExtractPlugin()
	]
};