/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './inc/**/*.php',
    './template-parts/**/*.php',
    './assets/js/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Outfit', 'system-ui', 'sans-serif'],
        serif: ['Playfair Display', 'ui-serif', 'Georgia', 'serif'],
      },
      colors: {
        brand: {
          ink: '#263238',
          cream: '#FFFCF8',
          navy: '#4A6C7C',
        },
        chroma: {
          red: '#D67D6B',
          redLight: '#F4E5E2',
          orange: '#C26524', // Darkened from #E89654 for 4.5:1 contrast
          orangeLight: '#FEF0E6',
          blue: '#4A6C7C',
          blueDark: '#2F4858',
          blueLight: '#E3E9EC',
          teal: '#4A6C7C',
          tealLight: '#E3E9EC',
          green: '#5E7066', // Darkened from #8DA399 for 4.5:1 contrast
          greenLight: '#E3EBE8',
          yellow: '#9C7835', // Darkened from #E6BE75 for 4.5:1 contrast
          yellowLight: '#FDF6E3',
        },
      },
      borderRadius: {
        '4xl': '2.5rem',
        '5xl': '3.5rem',
      },
    },
  },
  safelist: [
    // Pulse animation classes for status indicators
    'animate-pulse',
    'w-2',
    'h-2',
    'bg-chroma-green',
    'bg-chroma-blue',
    'rounded-full',
  ],
  plugins: [],
};
