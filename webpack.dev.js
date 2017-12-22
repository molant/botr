'use strict';

const path = require('path');
const package_info = require('./package.json');
const config_loader = require('./config-loader');
const webpack = require('webpack');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const FriendlyErrorsWebpackPlugin = require('friendly-errors-webpack-plugin');
const sass_extractor = new ExtractTextPlugin('style.css');

// 1. Javscript Constants

const constants = {
  PRODUCTION: false
};

// 2. Input
const SRC_DIR = path.resolve(__dirname, 'src');

// 3. Loading theme options
let theme_info = config_loader(package_info);

const OUTPUT_DIR = theme_info.output_dir;
theme_info.name = '[DEV] '+theme_info.name;



// 4. Summary
console.log('Wordpress Theme Boilerplate');
console.log('Will watch:', SRC_DIR);
console.log('Will output in:', OUTPUT_DIR);
console.log('');

console.log('Theme:', theme_info.name);
console.log('Version:', theme_info.version);

console.log('Author:', theme_info.author_name);
if (theme_info.author_email !== null) { console.log('Email:', theme_info.author_email); }
if (theme_info.author_uri !== null) { console.log('Author URI:', theme_info.author_uri); }
if (theme_info.description !== null) { console.log('Description:', theme_info.description); }
if (theme_info.license !== null) { console.log('License:', theme_info.license); }
if (theme_info.license_url !== null) { console.log('License URL:', theme_info.license_url); }
if (theme_info.tags !== null) { console.log('Tags:', theme_info.tags); }
if (theme_info.textdomain !== null) { console.log('Textdomain:', theme_info.textdomain); }



// 5. Creating CSS header
let css_header = `/*
Theme Name: ${theme_info.name}
Version: ${theme_info.version}
Author: ${theme_info.author_name}
`;

css_header += theme_info.author_email === null ? '' : 'Author Email:' +  theme_info.author_email + '\n';
css_header += theme_info.author_uri === null ? '' : 'Author URI:' +  theme_info.author_uri + '\n';
css_header += theme_info.description === null ? '' : 'Description:' +  theme_info.description + '\n';
css_header += theme_info.license === null ? '' : 'License:' + theme_info.license + '\n';
css_header += theme_info.license_url === null ? '' : 'License URL:' +  theme_info.license_url + '\n';
css_header += theme_info.tags === null ? '' : 'Tags:' +  theme_info.tags + '\n';
css_header += theme_info.textdomain === null ? '' : 'Textdomain:' +  theme_info.textdomain + '\n';

css_header += '\n*/';






module.exports = {

  context: __dirname,
  cache: false,
  watch: true,
  watchOptions: { ignored: /node_modules/ },
  devtool: 'inline-sourcemap',
  entry: { 'index': './src/index.js' },
  output: {
    path: OUTPUT_DIR,
    filename: '[name].js'
  },

  module: {
    rules: [
      { test: /.js$/, exclude: /node_modules/, use: 'babel-loader' },
      { test: /.html$/, use: 'html-loader' },
      {
        test: /\.scss$/,
        use: sass_extractor.extract([
          'css-loader',
          'postcss-loader',
          { loader: 'sass-loader', options: { data: css_header} } // TODO: The header should only be prepended once
        ])
      }
    ]
  },

  resolve: {
    alias: {
      'components': path.join(SRC_DIR, 'components'),
      'js': path.join(SRC_DIR, 'js'),
      'scss': path.join(SRC_DIR, 'scss/')
    }
  },

  plugins: [
    new FriendlyErrorsWebpackPlugin(),
    new webpack.DefinePlugin({ constants }),
    sass_extractor,
    new CopyWebpackPlugin([
      { context: 'src/static', from: '**/*', to: OUTPUT_DIR }
    ])
  ]

};
