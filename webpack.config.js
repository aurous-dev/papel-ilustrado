const path = require("path");
const UglifyJSPlugin = require("uglifyjs-webpack-plugin");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
// const VueLoaderPlugin = require('vue-loader-plugin');
const VueLoaderPlugin = require("vue-loader/lib/plugin");
const CopyPlugin = require("copy-webpack-plugin");
const WebpackMessages = require("webpack-messages");
const FriendlyErrorsWebpackPlugin = require("friendly-errors-webpack-plugin");
const WebpackNotifierPlugin = require("webpack-notifier");

module.exports = {
  // mode: "production",
  mode: "development",
  // lintOnSave: process.env.NODE_ENV !== 'production',
  entry: [
    "./wp-content/themes/papel-ilustrado/js/app.js",
    "./wp-content/themes/papel-ilustrado/scss/index.scss"
  ],
  output: {
    filename: "./wp-content/themes/papel-ilustrado/js/app.min.js",
    path: path.resolve(__dirname)
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        loader: "vue-loader"
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader",
          options: { presets: ["@babel/preset-env"] }
        }
      },
      {
        test: /\.(sass|scss|css)$/,
        use: [
          MiniCssExtractPlugin.loader,
          "css-loader",
          "resolve-url-loader",
          "sass-loader"
        ]
      },
      {
        test: /\.(png|jpg|svg|gif)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
        use: [
          {
            loader: "url-loader",
            options: {
              limit: 90000,
              fallback: "file-loader"
            }
          }
        ]
      },
      {
        test: /\.(ttf|eot|woff|woff2)$/,
        use: [
          {
            loader: "url-loader"
          }
        ]
      }
    ]
  },
  plugins: [
    new MiniCssExtractPlugin({
      filename: "./wp-content/themes/papel-ilustrado/css/index.min.css"
    }),
    new VueLoaderPlugin(),
    new WebpackMessages({
      name: "develop",
      logger: (str) => console.log(`>> ${str}`)
    }),
    new FriendlyErrorsWebpackPlugin(),
    new WebpackNotifierPlugin({ alwaysNotify: true })
  ],
  optimization: {
    minimizer: [
      new UglifyJSPlugin({
        cache: false,
        parallel: true
      }),
      new OptimizeCSSAssetsPlugin({})
    ]
  },
  // externals: {
  //   jquery: "jQuery",
  // },
  performance: { hints: false }
};
