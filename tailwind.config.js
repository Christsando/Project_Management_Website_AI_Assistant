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
                
                // Background color
                gradientBlue: '#002C71',
                orangeBg: '#FF7700',
                gradientOrange: '#9e4c04',
                greenBg: '#22C55E',
                gradientGreen: '#076328',

                // barChart color
                blueColor: '#2556A1',

                primary: '#3B82F6',
                purple: '#8B5CF6',
                green: '#22C55E',

                primaryText: '#1E293B',
                secondaryText: '#64748B',

                // button zoom
                zoombutton: '#264B83',

                cardSection: '#F6F6F6',
            },
        },
    },

    plugins: [forms],
};
