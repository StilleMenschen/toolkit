module.exports = {
  publicPath: process.env.BASE_URL,
  productionSourceMap: false,
  devServer: {
    port: 3000,
    proxy: {
      "/redis.php": { target: "http://localhost/redis.php" }
    }
  },
  transpileDependencies: ["vuetify"]
};
