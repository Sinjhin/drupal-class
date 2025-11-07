// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  devServer: {
    port: 8080,
    host: '0.0.0.0',
    https: {
      key: './server.key',
      cert: './server.crt'
    }
  },

  runtimeConfig: {
    public: {
      drupalBaseUrl: 'https://web.4rd.ai:3001'
    }
  }
})
