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

mix.ts('resources/js/app.ts', 'public/js')
    .sass('resources/sass/app.scss', 'public/css')
    .webpackConfig({
        module: {
          rules: [
            {
              test: /\.tsx?$/,
              loader: "ts-loader",
              options: { appendTsSuffixTo: [/\.vue$/] },
              exclude: /node_modules/
            },
            {
                test: /\.ts$/,
                exclude: /node_modules|vue\/src/,
                loader: "ts-loader",
                options: {
                  appendTsSuffixTo: [/\.vue$/]
                }
              },
          ]
        },
        resolve: {
          extensions: ["*", ".js", ".jsx", ".vue", ".ts", ".tsx"]
        }
      });