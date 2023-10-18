/**
 * Configuration webpack.
 * 
 * @link https://imranhsayed.medium.com/set-up-webpack-and-babel-for-your-wordpress-theme-4ab56a00c873
 * 
 */

const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const TerserPlugin = require("terser-webpack-plugin");
const { CleanWebpackPlugin } = require('clean-webpack-plugin');


const SRC_DIR = path.resolve(__dirname, 'src');
const JS_DIR = path.resolve(__dirname, 'src/js');
const PAGES_DIR = path.resolve(__dirname, 'src/js/pages');
const BUILD_DIR = path.resolve(__dirname, 'dist');

const entry = {
    index: SRC_DIR + '/index.js',

    // Separated compilation file (for any other page added after this line you need restart webpack)
    home: PAGES_DIR + '/home.js',
    login: PAGES_DIR + '/login.js',
    register: PAGES_DIR + '/register.js',
    auth_user: PAGES_DIR + '/auth_user.js',
    profile: PAGES_DIR + '/profile.js',
    new_auction: PAGES_DIR + '/new_auction.js',
    archive_auctions: PAGES_DIR + '/archive_auctions.js',
    archive_shop: PAGES_DIR + '/archive_shop.js',
    single_auction: PAGES_DIR + '/single_auction.js',
    cart: PAGES_DIR + '/cart.js',
    my_orders: PAGES_DIR + '/my_orders.js',
};
const output = {
    path: BUILD_DIR,
    filename: 'js/[name].bundle.js',
    clean: true
};

const rules = [
    {
        test: /\.js$/,
        include: [JS_DIR],
        exclude: /node_modules/,
        use: 'babel-loader'
    }, {
        test: /\.(sa|sc|c)ss$/,
        use: [
            MiniCssExtractPlugin.loader,
            "css-loader",
            "sass-loader"
        ],
    }, {
        test: /\.(png|jpg|svg|jpeg|gif|ico)$/,
        use: [
            {
                loader: 'file-loader',
                options: {
                    name: '[path][name].[ext]',
                    publicPath: 'production' === process.env.NODE_ENV ? './' : '../',
                },
            },
        ],
    },
    {
        test: /\.(ttf|otf|eot|svg|woff(2)?)(\?[a-z0-9]+)?$/,
        use: {
            loader: 'file-loader',
            options: {
                name: '[path][name].[ext]',
                publicPath: 'production' === process.env.NODE_ENV ? './' : '../'
            }
        }
    }
];

const plugins = (argv) => [

    new CleanWebpackPlugin({
        cleanStateWebpackAssets: ('production' === argv.mode),
    }),

    new MiniCssExtractPlugin({
        filename: 'css/[name].bundle.css'
    }),
];

module.exports = (env, argv) => ({

    entry: entry,

    output: output,

    devtool: 'source-map',

    module: {
        rules: rules,
    },

    optimization: {
        minimize: 'production' === process.env.NODE_ENV ? true : false,
        minimizer: [
            new CssMinimizerPlugin({
                parallel: 4,
                minimizerOptions: {
                    preset: [
                        "default",
                        {
                            discardComments: { removeAll: 'production' === process.env.NODE_ENV ? true : false },
                        },
                    ],
                },
            }),
            new TerserPlugin({
                parallel: 4,
            }),
        ]
    },

    plugins: plugins(argv),
});