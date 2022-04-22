module.exports = {
    content: [
        'templates/**/*.html.twig',
        'assets/js/**/*.js',
        'vendor/symfony/twig-bridge/Resources/views/Form/tailwind_2_layout.html.twig'
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms')
    ],
}
