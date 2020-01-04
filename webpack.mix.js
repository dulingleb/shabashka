const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

const TSLintPlugin = require('tslint-webpack-plugin');

mix.ts('resources/js/app.ts', 'public/js')
    .sass('resources/scss/app.scss', 'public/css')
    .webpackConfig({
        module: {
          rules: [
            {
              test: /\.tsx?$/,
              loader: "ts-loader",
              options: {
                appendTsSuffixTo: [/\.vue$/],
                transpileOnly : true
              },
              exclude: /node_modules/
            },
            {
                test: /\.ts$/,
                exclude: /node_modules|vue\/src/,
                loader: "ts-loader",
                options: {
                  appendTsSuffixTo: [/\.vue$/],
                  transpileOnly : true
                }
              },
          ]
        },
        resolve: {
          extensions: ["*", ".js", ".jsx", ".vue", ".ts", ".tsx"]
        },
        plugins: [
          new TSLintPlugin({
              files: [
                './resources/js/**/*.ts'
              ]
          })
      ]
      });