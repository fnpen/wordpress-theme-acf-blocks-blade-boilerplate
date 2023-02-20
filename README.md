# WordPress Theme Boilerplate using ACF Pro Blocks and Blade as template engine

## Motivation

Creating the boilerplate of theme for fast and rapid WordPress Theme using Gutenberg blocks, together with ACF Pro and blade.

ACF Pro plugin is required.

## What's inside

* Uses Blade to render blocks on server-side with simple markup

* 🤹🏻  Keep as simple as possible

* Based on Twenty Twenty Three theme

* 🪶  Lightweght assets and WebPack
	* Using **wp-scripts** package with WebPack underhood to build bundle with minification on production mode

* Uses FontSource to use fonts locally and embed them to theme

* 🧑‍🎨  PostCSS with stage-0 features to use any CSS-next features

* ⚡  Great developer experience
	* 🚀 Supports multi block compiling using one command to start.

* Examples
	* Tesmonial
	* Tesmonial Item - example using Inner Blocks.

* 🔬  Coding standarts
	* Phpcs and wp-coding-standards is available
	* Prettier is used to format js, css files

* 🌏  Translation Ready
	* [ ] 📝  Blade helpers for translation methods
	* [ ] 📝  Use @wordpress/i18n and __();
	* [ ] 📥  Generate pot and json files for translation

## How to try on my WordPress site? 🤔 

Clone the repo using next command to your `wp-content/themes/` directory:

```
git clone https://github.com/fnpen/wordpress-theme-acf-blocks-blade-boilerplate.git
```

Enable plugin 'WordPress Theme Boilerplate using ACF Pro Blocks and Blade'.

## More Examples



## How to modify and build bundle? 😎 

Install nodejs and npm to your system,

Install all packages :

```
npm install

// if you use pnpm:

pnpm i
```

Use the next command to build the bundle in development mode:

```
npm run start
```

# 🥳 

Have a great time with developing!

> The bundle will be rebuilt on any file change.

Use the next command to build the bundle in production mode:

```
npm build
```
