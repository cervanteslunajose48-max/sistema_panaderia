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
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                display: ['Poppins', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                'bakery': {
                    // Primary palette
                    'gold': '#D4A373',
                    'gold-light': '#E9C46A',
                    'gold-dark': '#B8956A',

                    // Neutrals
                    'cream': '#FAEDCD',
                    'cream-light': '#FAF9F6',
                    'cream-dark': '#F0E5C5',

                    // Dark tones
                    'dark': '#3E2723',
                    'dark-light': '#5D4037',
                    'dark-deep': '#2C1B18',

                    // Accents
                    'light': '#FEFFAE',
                    'red': '#BC4749',
                    'red-light': '#D16466',
                    'green': '#606C38',
                    'green-light': '#7F8C5A',

                    // Pastel variations
                    'peach': '#FFBB98',
                    'peach-light': '#FFD7B8',
                    'orange': '#F4A261',
                    'orange-light': '#FFB380',
                    'vanilla': '#FFE5B4',
                    'coral': '#FCA17D',
                },
                // Additional professional palette
                'pastel': {
                    'pink': '#FFB3BA',
                    'orange': '#FFDFBA',
                    'yellow': '#FFFFBA',
                    'green': '#BAFFC9',
                    'blue': '#BAE1FF',
                    'purple': '#E0BBE4',
                    'teal': '#B5EAD7',
                }
            },
            boxShadow: {
                'soft': '0 2px 15px rgba(0, 0, 0, 0.08)',
                'softer': '0 2px 8px rgba(0, 0, 0, 0.05)',
                'glow': '0 0 20px rgba(212, 163, 115, 0.3)',
                'glow-strong': '0 0 30px rgba(212, 163, 115, 0.5)',
                'inner-soft': 'inset 0 2px 4px rgba(0, 0, 0, 0.06)',
            },
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
                'fade-in-up': 'fadeInUp 0.5s ease-out',
                'slide-in': 'slideIn 0.3s ease-out',
                'bounce-subtle': 'bounceSubtle 1s infinite',
                'pulse-soft': 'pulseSoft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'shimmer': 'shimmer 2s linear infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideIn: {
                    '0%': { transform: 'translateX(-10px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                bounceSubtle: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-5px)' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.8' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-1000px 0' },
                    '100%': { backgroundPosition: '1000px 0' },
                },
            },
            backdropBlur: {
                xs: '2px',
            },
            transitionDuration: {
                '400': '400ms',
            },
        },
    },

    plugins: [forms],
};
