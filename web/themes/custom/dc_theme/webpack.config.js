const path = require('path');
const isDevMode = process.env.NODE_ENV !== 'production';

const config = {
  entry: {
    homeCarousel: ["./js/src/homeCarousel/index.jsx"],
    homeSlideRecent: ["./js/src/homeSlideRecent/index.jsx"],
    search: ["./js/src/search/index.jsx"],
    like: ["./js/src/like/index.jsx"],
    send: ["./js/src/send/index.jsx"],
  },
  devtool: (isDevMode) ? 'source-map' : false,
  mode: (isDevMode) ? 'development' : 'production',
  output: {
    path: isDevMode ? path.resolve(__dirname, "js/dist_dev") : path.resolve(__dirname, "js/dist"),
    filename: '[name].min.js'
  },
  resolve: {
    extensions: ['.js', '.jsx'],
  },
  module: {
    rules: [
      {
        test: /\.jsx?$/,
        loader: 'babel-loader',
        exclude: /node_modules/,
        include: path.join(__dirname, 'js/src'),
      },
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader'],
      },
      // Add a new rule for image files
      {
        test: /\.(jpg|jpeg|png|gif|svg)$/,
        use: {
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'images/', // Output directory for images
          },
        },
      },
    ],
  },
};

module.exports = config;
