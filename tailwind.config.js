import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                playfair: ['"Playfair Display"', 'serif'],
                lato: ['"Lato"', 'sans-serif'],
            },
            colors: {
                important: '#F59E0B',
                importantHover: '#d28a0c',

                primary: '#3B82F6',

                primaryText: '#1E293B',
                secondaryText: '#64748B',

                cardSection: '#F6F6F6',
            },
        },
    },

    plugins: [forms],
};
