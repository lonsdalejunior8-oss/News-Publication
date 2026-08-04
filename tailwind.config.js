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
            },
            colors: {
                // Solomon Islands national flag colors (blue / yellow / green)
                sig: {
                    blue: '#0051BA',
                    'blue-dark': '#003C8A',
                    yellow: '#FCD116',
                    green: '#215B33',
                },
            },
        },
    },

    plugins: [forms],
};
