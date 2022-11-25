const { defineConfig } = require('cypress')

module.exports = defineConfig({
    e2e:{
        projectId: "ifc328",
        supportFile: false,
        specPattern: "**/*.spec.js",
        retries: 2,
        videoUploadOnPasses: false,
        defaultCommandTimeout: 10000
    }
})
