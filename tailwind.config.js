module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'vert-energie': '#2E8B57',      // Vert énergie principal
        'orange-burkina': '#FF8C00',    // Orange du Burkina
        'gris-moderne': '#4A5568',      // Gris moderne
        'bleu-tech': '#3182CE',         // Bleu technologique
      },
      fontFamily: {
        'montserrat': ['Montserrat', 'sans-serif'],
        'roboto': ['Roboto', 'sans-serif'],
        'open-sans': ['Open Sans', 'sans-serif'],
      },
      backgroundImage: {
        'gradient-hero': 'linear-gradient(135deg, #2E8B57 0%, #3182CE 100%)',
        'gradient-cta': 'linear-gradient(135deg, #FF8C00 0%, #2E8B57 100%)',
      }
    },
  },
  plugins: [],
}
