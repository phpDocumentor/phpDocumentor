module.exports = {
  purge: {
    enabled: true,
    content: [
        './js/src/*.js',
        './*.html.twig',
        './**/*.html.twig'
    ]
  },
  darkMode: "class",
  theme: {
    extend: {},
  },
  variants: {
    extend: {},
  },
  plugins: [],
}
