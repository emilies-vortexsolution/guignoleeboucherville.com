# Requirements
| Prerequisite       | How to check    | How to install
| ------------------ | --------------- | --------------- |
| PHP >= 7.x.x       | `php -v`        | [kms : installer PHP ](https://alternatif.local.vortexsolution.com/~wpkms/article/outils-environnement/installer-php-sous-windows-10/) |
| Composer >= 1.6.x  | `composer -V`   | [getcomposer.org](https://getcomposer.org/download/) |
| Node.js >= 6.9     | `node -v`       | [nodejs.org](http://nodejs.org/) |
| gulp-cli >= 2.0.0  | `gulp -v`       | `npm install -g gulp-cli` |

# Install
`npm install`

# Compile files
## Build
`gulp`

`gulp --production`

## Watch
you need to edit assets/manifest.json to add your local url

`gulp watch` 

`gulp watch --browser` will add browserSync 

## Code Standards and quality inspection
We are using custom coding standards rules based on [WordPress-Extra standard](https://github.com/WordPress/WordPress-Coding-Standards).
The rules are defined in phpcs.ruleset.xml.
PHP CodeSniffer can be run with command : `composer phpcs`.

# Add plugins
Use our [required plugin generator](http://plugins.wp.vortexdev.com/) to generate  a .json file.
Put this file in /required-plugins. 

You can add multiple files. If 

# WPML Config File
[To-do]

# More documentation
- [Technical documentation](_doc/dev/DOC-TECH.md)
- [User documentation](_doc/client/index.md)

[Changelog](CHANGELOG.md)