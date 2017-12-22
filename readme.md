# Webpack Wordpress Theme boilerplate

> Create custom Wordpress Themes from scratch using HTML5, ES6 and SASS with Webpack.

Focus on the actual development, everything else is done for you.

* All your modifications are updated live
* Leverage npm semantic versionning
* Bundles your theme in a deployment ready zip file.


## Installation

Download the sources from GitHub to your local dev environnement. Open a terminal in that directory and install using npm :

```sh
npm install
```

## Usage

During development:
```sh
npm run dev
```

Production build:
```sh
npm run build
```


## Configuration

Everything is configured using the package.json file. You need to configure at least wp_boilerplate.wp_location for this package to work.




### author

This is your name. You can include an email address and an url (in any order) in that field, they will be added semantically to the theme information.

Format:

* "Your Name"
* "Your Name <you.name@email.com>"
* "Your Name <you.name@email.com> (http://example.com)"
* "Your Name (https://example.com)"

**Note:** multiple contributors are not supported.


### description

You can add a description in that field.




### keywords

This array will contain the tags of your theme. [Refer to this page](https://make.wordpress.org/themes/handbook/review/required/theme-tags/ "Theme Tags – Theme Review Team — WordPress")  if you want to use that option.




### license

[Learn about Wordpress theme licensing](https://developer.wordpress.org/themes/getting-started/wordpress-licensing-the-gpl/ "Theme Developer Handbook") and [npm licensing](https://docs.npmjs.com/files/package.json#license "npm Documentation").





### wp_boilerplate.name

This will be the name of your theme. It will also be used to name the dev folder in your wordpress installation and the final zip file.

If the theme name is **"Twenty Thirteen"**, the the dev folder will be **"twenty-thirteen-dev"** and the final zip file will be **"twenty-thirteen-[version].zip"**.



### wp_boilerplate.wp_location

Configure here the **absolute path** to your wordpress installation.




### wp_boilerplate.license_url

An optionnal link to your lincense.




### wp_boilerplate.textdomain

[The textdomain](https://developer.wordpress.org/themes/functionality/internationalization/#text-domains "Theme Developer Handbook") used by your theme for translation.





## Configuration example (package.json)


```json
{

  "...": "...",

  "version": "4.2.0",
  "author": "John Doe <john.doe@example.com> (https://example.com/)",
  "description": "This Awesome Theme is all you need.",
  "keywords": [
    "one-column",
    "left-sidebar",
    "buddypress",
    "e-commerce"
  ],
  "license": "GPL-3.0",
  "wp_boilerplate": {
    "name": "The Awesome Theme",
    "wp_location": "/var/www/html/wordpress",
    "license_url": "https://www.gnu.org/licenses/gpl-3.0-standalone.html",
    "textdomain": "awesometheme"
  },

  "...": "..."

}
```
