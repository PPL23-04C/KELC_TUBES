/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            colors: {
                primary: "#2563eb",
                secondary: "#0f172a",
                success: "#22c55e",
                warning: "#f59e0b",
                background: "#f8fafc",
            },
            fontFamily: {
                sans: ['Inter', 'Poppins', 'sans-serif'],
            }
        },
    },
    plugins: [],
}
