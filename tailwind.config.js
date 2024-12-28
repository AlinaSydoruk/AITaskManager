/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')
module.exports = {
  content: [
    "./vendor/tales-from-a-dev/flowbite-bundle/templates/**/*.html.twig",
    "./assets/**/*.js",
    "./templates/**/*.html.twig",
  ],
  theme: {
    extend: {},
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      black: colors.black,
      white: colors.white,
      red: colors.red,
      green: colors.green,
      gray: colors.gray,
      orange: colors.orange,
      sky: colors.sky,
      cyan: colors.cyan,
      neutral: colors.neutral,
      yellow: colors.yellow,
    },
  },
  plugins: [],
}




