import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                "tertiary": "#70000c",
                "inverse-surface": "#213145",
                "on-primary-fixed-variant": "#173bab",
                "on-surface-variant": "#444653",
                "on-surface": "#0b1c30",
                "on-secondary-fixed-variant": "#005236",
                "on-secondary": "#ffffff",
                "inverse-on-surface": "#eaf1ff",
                "primary-container": "#1e40af",
                "primary-fixed-dim": "#b8c4ff",
                "outline": "#757684",
                "on-tertiary-container": "#ffa39c",
                "secondary-container": "#6cf8bb",
                "on-background": "#0b1c30",
                "on-secondary-fixed": "#002113",
                "surface-dim": "#cbdbf5",
                "on-primary-container": "#a8b8ff",
                "surface-container-highest": "#d3e4fe",
                "background": "#f8f9ff",
                "on-tertiary-fixed": "#410004",
                "error": "#ba1a1a",
                "error-container": "#ffdad6",
                "surface-bright": "#f8f9ff",
                "surface": "#f8f9ff",
                "on-error": "#ffffff",
                "primary": "#00288e",
                "surface-container-low": "#eff4ff",
                "surface-container": "#e5eeff",
                "on-tertiary": "#ffffff",
                "surface-container-high": "#dce9ff",
                "tertiary-container": "#9b0015",
                "tertiary-fixed": "#ffdad7",
                "on-secondary-container": "#00714d",
                "tertiary-fixed-dim": "#ffb3ad",
                "on-primary": "#ffffff",
                "outline-variant": "#c4c5d5",
                "on-tertiary-fixed-variant": "#930013",
                "inverse-primary": "#b8c4ff",
                "secondary-fixed": "#6ffbbe",
                "surface-container-lowest": "#ffffff",
                "primary-fixed": "#dde1ff",
                "surface-tint": "#3755c3",
                "on-error-container": "#93000a",
                "surface-variant": "#d3e4fe",
                "on-primary-fixed": "#001453",
                "secondary-fixed-dim": "#4edea3",
                "secondary": "#006c49"
            },
            borderRadius: {
                "DEFAULT": "0.25rem",
                "lg": "0.5rem",
                "xl": "0.75rem",
                "full": "9999px"
            },
            spacing: {
                "xs": "4px",
                "gutter": "24px",
                "margin-mobile": "16px",
                "margin-desktop": "48px",
                "base": "8px",
                "xl": "32px",
                "md": "16px",
                "sm": "8px",
                "lg": "24px"
            },
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                "display-lg-mobile": ["Inter"],
                "body-sm": ["Inter"],
                "headline-md": ["Inter"],
                "body-lg": ["Inter"],
                "label-sm": ["Inter"],
                "label-md": ["Inter"],
                "headline-sm": ["Inter"],
                "headline-lg-mobile": ["Inter"],
                "headline-lg": ["Inter"],
                "body-md": ["Inter"],
                "display-lg": ["Inter"]
            },
            fontSize: {
                "display-lg-mobile": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
                "label-sm": ["12px", {"lineHeight": "16px", "fontWeight": "500"}],
                "label-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0.05em", "fontWeight": "500"}],
                "headline-sm": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                "headline-lg-mobile": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}]
            }
        },
    },

    plugins: [forms],
};
