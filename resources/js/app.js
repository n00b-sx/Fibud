import './bootstrap';
import 'preline';

/**
 * OpenMoji client-side helper to resolve any Emoji string to OpenMoji SVG CDN URL.
 */
window.toOpenMojiSvg = function(emoji) {
    if (!emoji) return '';
    const codePoints = [];
    for (const char of emoji) {
        const code = char.codePointAt(0);
        if (code !== 0xFE0F) {
            codePoints.push(code.toString(16).toUpperCase());
        }
    }
    const hex = codePoints.join('-');
    return `https://cdn.jsdelivr.net/npm/openmoji@15.1.0/color/svg/${hex}.svg`;
};

window.addEventListener('load', () => {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
});
