import defaultConfig from '@wordpress/scripts/config/webpack.config.js';
import clone from 'deep-clone';
import { config } from 'dotenv';
import MiniCSSExtractPlugin from 'mini-css-extract-plugin';
import path from 'path';

import { globby } from 'globby';

const blocks = await globby(['./blocks/*/block.json']);

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

config();

import { existsSync } from 'fs';
import { fileURLToPath } from 'url';

const createConfig = ({ name, entry, dir, externals }) => {
	const isProduction = process.env.NODE_ENV === 'production';

	const config = {
		...clone(defaultConfig),
		name,
		entry,
		context: path.resolve(__dirname, dir),
		module: {
			...defaultConfig.module,
			rules: [
				{
					resourceQuery: /inline/,
					test: /\.(bmp|png|jpe?g|gif)$/i,
					type: 'asset/inline',
				},
				{
					test: /\.md$/i,
					type: 'asset/source',
				},
				...defaultConfig.module.rules,
			],
		},

		watchOptions: {
			followSymlinks: true,
			ignored: ['node_modules/**'].filter(Boolean),
		},

		resolveLoader: {
			modules: [
				path.resolve(__dirname, dir, 'node_modules'),
				path.resolve(__dirname, dir, './'),
				path.resolve(__dirname, 'node_modules'),
			],
		},

		stats: {
			children: false,
			all: false,
			entrypoints: true,
			warnings: true,
			errors: true,
			hash: false,
			timings: true,
			errorDetails: true,
			builtAt: true,
		},

		experiments: {
			topLevelAwait: true,
		},

		resolve: {
			...defaultConfig.resolve,
			extensions: ['.tsx', '.ts', '.js', '.jsx', '.json'],
			alias: {
				...defaultConfig.resolve.alias,
			},
		},

		optimization: {
			...defaultConfig.optimization,
			removeEmptyChunks: true,

			splitChunks: {
				cacheGroups: {
					internalStyle: {
						type: 'css/mini-extract',
						test: /[\\/]+?\.(sc|sa|c)ss$/,
						chunks: 'all',
						enforce: true,
					},
					default: false,
				},
			},
		},

		externals: {
			...defaultConfig.externals,
			...(externals || {}),
		},

		output: {
			...defaultConfig.output,
			filename: '[name].js',
			path: path.resolve(__dirname, dir, 'build'),
		},

		plugins: [
			new MiniCSSExtractPlugin({
				filename: '[name].css',
			}),

			...defaultConfig.plugins.filter(
				(plugin) => !['LiveReloadPlugin'].includes(plugin.constructor.name)
			),
		],
	};

	if (!isProduction) {
		config.devServer.host = '127.0.0.1';
		config.devServer.allowedHosts = 'all';

		config.devServer.server = {
			type: 'https',
			options: {
				key: process.env['TSL_KEY'],
				cert: process.env['TSL_CERT'],
				requestCert: false,
			},
		};
	} else {
		delete config.devServer;
	}

	return config;
};

export default [
	createConfig({
		name: 'theme-css',
		dir: './',
		entry: {
			theme: './src/theme.js',
		},
	}),
	...blocks
		.map((blockDir) => {
			const dir = path.dirname(blockDir) + '/';

			let entry;

			if (existsSync(dir + '/script.js')) {
				entry = './script.js';
			} else if (existsSync(dir + '/style.css')) {
				entry = './style.css';
			}

			return entry
				? createConfig({
						name: `block/${path.basename(dir)}`,
						dir,
						entry: {
							block: entry,
						},
				  })
				: null;
		})
		.filter(Boolean),
];
