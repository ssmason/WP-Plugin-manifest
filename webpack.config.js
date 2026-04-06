/**
 * Webpack configuration for Satori Manifest.
 *
 * Compiles block JS and SCSS sources into the build directories.
 *
 * @package SatoriManifest
 * @author  Stephen Mason <steve@satori-digital.com>
 * @since   1.0.0
 */

const path                 = require( 'path' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );

const isProduction = process.env.NODE_ENV === 'production';

module.exports = {
	entry: {
		// Block editor JS.
		'block/build/index': './block/src/index.js',
		// Admin options page JS.
		'block/build/admin': './block/src/admin.js',
		// SCSS entries.
		'src/scss/admin/admin': './src/scss/admin/admin.scss',
		'src/scss/editor/editor': './src/scss/editor/editor.scss',
		'src/scss/frontend/frontend': './src/scss/frontend/frontend.scss',
	},
	output: {
		path: path.resolve( __dirname ),
		filename: '[name].js',
	},
	module: {
		rules: [
			{
				test: /\.jsx?$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						presets: [
							'@babel/preset-env',
							[
								'@babel/preset-react',
								{ runtime: 'automatic' },
							],
						],
					},
				},
		},
			{
				test: /\.scss$/,
				use: [
					MiniCssExtractPlugin.loader,
					'css-loader',
					'sass-loader',
				],
		},
		],
	},
	plugins: [
		new MiniCssExtractPlugin(
			{
				filename: ( pathData ) => {
					// Output CSS next to the SCSS entry, in a build/ subdir.
					const name = pathData.chunk.name;
					if ( name.startsWith( 'src/scss/' ) ) {
						const parts = name.split( '/' );
						// e.g. src/scss/admin/admin → src/scss/admin/build/admin.css
						parts.splice( parts.length - 1, 0, 'build' );
						return parts.join( '/' ) + '.css';
					}
					return '[name].css';
				},
			}
		),
	],
externals: {
	'@wordpress/blocks': [ 'wp', 'blocks' ],
	'@wordpress/block-editor': [ 'wp', 'blockEditor' ],
	'@wordpress/components': [ 'wp', 'components' ],
	'@wordpress/element': [ 'wp', 'element' ],
	'@wordpress/i18n': [ 'wp', 'i18n' ],
	'@wordpress/data': [ 'wp', 'data' ],
	'@wordpress/icons': [ 'wp', 'icons' ],
	},
	resolve: {
		extensions: [ '.js', '.jsx' ],
	},
	mode: isProduction ? 'production' : 'development',
	devtool: isProduction ? false : 'source-map',
};
