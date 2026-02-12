module.exports = {
  testEnvironment: 'node',
  coverageDirectory: 'coverage',
  collectCoverageFrom: [
    'src/models/**/*.js',
    'src/services/**/*.js',
    '!src/config/**',
    '!**/node_modules/**'
  ],
  testMatch: [
    '**/tests/unit/**/*.test.js',
    '**/tests/integration/**/*.test.js'
  ],
  coverageThreshold: {
    'src/services/': {
      branches: 80,
      functions: 75,
      lines: 80,
      statements: 80
    }
  },
  verbose: true,
  testTimeout: 10000
};
