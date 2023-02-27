/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./assets/**/*.js",
        "./templates/**/*.html.twig",
    ],
    theme: {
        extend: {},
        container: {
            center: true,
            padding: '1rem',
        }
    },
    plugins: [],
}
