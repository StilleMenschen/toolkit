const CompressionWebpackPlugin = require("compression-webpack-plugin");

module.exports = {
  publicPath: process.env.BASE_URL,
  productionSourceMap: false,
  configureWebpack: config => {
    if (process.env.NODE_ENV === "production") {
      // 为生产环境修改配置...
      config.plugins.push(
        new CompressionWebpackPlugin({
          test: /\.(js|css|json|txt|html|ico|svg)(\?.*)?$/i, //处理所有匹配此 {RegExp} 的资源
          threshold: 249856 //只处理比这个值大的资源。按字节计算(设置244KB以上进行压缩)
        })
      );
    } else {
      // 为开发环境修改配置...
      config.devtool = "source-map";
    }
  },
  devServer: {
    port: 3000,
    proxy: {
      "/redis.php": { target: "http://localhost/redis.php" }
    }
  },
  transpileDependencies: ["vuetify"]
};
