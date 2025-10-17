/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./resources/**/*.scss",
    "./resources/**/*.css",
    "./app/**/*.php",
  ],
  theme: {
    extend: {
      screens: {
        // Breakpoints críticos específicos
        'smartwatch': '280px',    // Smartwatch crítico
        'mobile': '320px',        // Mobile básico
        'foldable': '720px',      // Dispositivos plegables crítico
        'tablet': '768px',        // Tablet estándar
        'desktop': '1024px',      // Desktop estándar
        '4k': '3840px',          // 4K crítico
        
        // Breakpoints adicionales
        '3xl': '1600px',
        '4xl': '2560px',
        '5xl': '3840px',
        
        // Max-width queries para componentes específicos
        'max-smartwatch': { 'max': '279px' },
        'max-mobile': { 'max': '479px' },
        'max-tablet': { 'max': '767px' },
        'max-foldable': { 'max': '719px' },
        'max-desktop': { 'max': '1023px' },
        
        // Rangos específicos
        'only-smartwatch': { 'min': '280px', 'max': '319px' },
        'only-mobile': { 'min': '320px', 'max': '719px' },
        'only-foldable': { 'min': '720px', 'max': '1023px' },
        'only-desktop': { 'min': '1024px', 'max': '3839px' },
      },
      colors: {
        'gir-primary': {
          50: '#fef7e7',
          100: '#fdebc0',
          200: '#fbde99',
          300: '#f9d072',
          400: '#f7c34b',
          500: '#D1A854',  // Color corporativo principal
          600: '#b8954a',
          700: '#9f8240',
          800: '#866f36',
          900: '#6d5c2c',
        },
        'gir-gold': {
          50: '#fef9ec',
          100: '#fdf0c9',
          200: '#fce7a6',
          300: '#fade83',
          400: '#f9d560',
          500: '#EDC979',  // Color corporativo dorado claro
          600: '#d4b569',
          700: '#bba159',
          800: '#a28d49',
          900: '#897939',
        },
        'gir-dark-gold': {
          50: '#f7f3e6',
          100: '#ebe1b8',
          200: '#dfcf8a',
          300: '#d3bd5c',
          400: '#c7ab2e',
          500: '#8b6914',  // Color corporativo dorado oscuro
          600: '#7d5f12',
          700: '#6f5510',
          800: '#614b0e',
          900: '#53410c',
        },
        'gir-warm-gray': {
          50: '#f9f7f4',
          100: '#f0ede6',
          200: '#e1dac9',
          300: '#cfc1a3',
          400: '#b9a47a',
          500: '#847D77',
          600: '#77715f',
          700: '#635d4f',
          800: '#524e43',
          900: '#45423a',
        }
      },
      fontFamily: {
        'sans': ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif'],
        'gir': ['Inter', 'Segoe UI', 'Tahoma', 'Geneva', 'Verdana', 'sans-serif'],
      },
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        // Espaciado compacto responsivo
        'compact-xs': '0.125rem',   // 2px
        'compact-sm': '0.25rem',    // 4px
        'compact-md': '0.5rem',     // 8px
        'compact-lg': '0.75rem',    // 12px
        'compact-xl': '1rem',       // 16px
        'compact-2xl': '1.5rem',    // 24px
      },
      fontSize: {
        // Tamaños de fuente responsivos
        'xs-responsive': 'clamp(0.5rem, 1.25vw, 0.75rem)',
        'sm-responsive': 'clamp(0.625rem, 1.5vw, 0.875rem)',
        'base-responsive': 'clamp(0.75rem, 2vw, 1rem)',
        'lg-responsive': 'clamp(0.875rem, 2.5vw, 1.125rem)',
        'xl-responsive': 'clamp(1rem, 3vw, 1.25rem)',
        '2xl-responsive': 'clamp(1.125rem, 3.5vw, 1.5rem)',
        '3xl-responsive': 'clamp(1.25rem, 4vw, 1.875rem)',
        '4xl-responsive': 'clamp(1.5rem, 5vw, 2.25rem)',
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-in': 'slideIn 0.3s ease-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
        'bounce-soft': 'bounceSoft 0.6s ease-in-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideIn: {
          '0%': { transform: 'translateY(-10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' },
        },
        bounceSoft: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-5px)' },
        }
      },
      boxShadow: {
        'gir': '0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06)',
        'gir-lg': '0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05)',
        'responsive': '0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06)',
        'responsive-md': '0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06)',
        'responsive-lg': '0 10px 15px rgba(0, 0, 0, 0.1), 0 4px 6px rgba(0, 0, 0, 0.05)',
      },
      gridTemplateColumns: {
        // Grid responsivo automático
        'auto-fit-sm': 'repeat(auto-fit, minmax(120px, 1fr))',
        'auto-fit-md': 'repeat(auto-fit, minmax(200px, 1fr))',
        'auto-fit-lg': 'repeat(auto-fit, minmax(280px, 1fr))',
        'auto-fit-xl': 'repeat(auto-fit, minmax(320px, 1fr))',
        
        // Grids específicos para breakpoints
        'responsive-1': 'repeat(1, 1fr)',
        'responsive-2': 'repeat(2, 1fr)',
        'responsive-3': 'repeat(3, 1fr)',
        'responsive-4': 'repeat(4, 1fr)',
        'responsive-6': 'repeat(6, 1fr)',
        'responsive-8': 'repeat(8, 1fr)',
      },
      maxWidth: {
        'responsive-xs': 'clamp(200px, 40vw, 300px)',
        'responsive-sm': 'clamp(300px, 50vw, 500px)',
        'responsive-md': 'clamp(400px, 60vw, 700px)',
        'responsive-lg': 'clamp(500px, 70vw, 900px)',
        'responsive-xl': 'clamp(600px, 80vw, 1200px)',
      },
      minHeight: {
        'component-xs': 'clamp(28px, 5vh, 40px)',
        'component-sm': 'clamp(32px, 6vh, 48px)',
        'component-md': 'clamp(40px, 8vh, 60px)',
        'component-lg': 'clamp(48px, 10vh, 80px)',
        'component-xl': 'clamp(60px, 12vh, 120px)',
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
  // Important: Use this to avoid conflicts with Bootstrap
  corePlugins: {
    preflight: false,
  },
  prefix: 'tw-',
}
