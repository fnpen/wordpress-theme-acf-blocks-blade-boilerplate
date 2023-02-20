# WordPress Theme Boilerplate using ACF Pro Blocks and Blade as template engine

![image](https://user-images.githubusercontent.com/31767378/220068812-ee7dd479-463a-4747-a545-af56d4a149fb.png)

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

* Better Local JSON - ACF Pro UI automatically reads and writes json to block's folder instead of `acf-json`

* Clean structure - each block has his own separate folder, build and automatically bundled by WebPack

* ⚡  Great developer experience
	* 🚀 Supports multi block compiling using one command to start.

* Examples
	* Testmonial
	* Testmonial Item - example using Inner Blocks.

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

Enable theme 'WordPress Theme Boilerplate using ACF Pro Blocks and Blade'.

## Clean Blocks Structure

![image](https://user-images.githubusercontent.com/31767378/220070171-ea8cd24f-3960-43f4-a07d-830247626ca4.png)

## Automatic block.json detection

![image](https://user-images.githubusercontent.com/31767378/220070370-6d8ce7f2-17f5-4697-a9e4-d51d3686eb64.png)


## More Examples

![image](https://user-images.githubusercontent.com/31767378/220068588-89db3ee4-4a35-4731-be2e-580f59d4de77.png)


Previews:

![image](https://user-images.githubusercontent.com/31767378/220069946-d9a5a8fb-65d2-4c56-85c2-280e28f34c99.png)

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
