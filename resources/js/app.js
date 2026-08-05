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

/**
 * JS Glassmorphism Controller for Preline Overlay Modals & Backdrops
 */
function applyGlassmorphismBackdrop() {
    // Select any Preline-generated backdrop element or overlay wrapper
    const backdropElements = document.querySelectorAll(
        '[data-hs-overlay-backdrop-template], .hs-overlay-backdrop, div[class*="bg-gray-900"][class*="fixed"]'
    );

    backdropElements.forEach(el => {
        el.style.setProperty('backdrop-filter', 'blur(16px) saturate(180%)', 'important');
        el.style.setProperty('-webkit-backdrop-filter', 'blur(16px) saturate(180%)', 'important');
        el.style.setProperty('background-color', 'rgba(15, 23, 42, 0.45)', 'important');
    });

    // Select all modal glass dialog containers
    const modalGlassElements = document.querySelectorAll('.modal-glass');
    modalGlassElements.forEach(el => {
        el.style.setProperty('backdrop-filter', 'blur(30px) saturate(200%)', 'important');
        el.style.setProperty('-webkit-backdrop-filter', 'blur(30px) saturate(200%)', 'important');
        el.style.setProperty('background-color', 'rgba(255, 255, 255, 0.75)', 'important');
    });
}

// Observe DOM mutations so dynamically inserted Preline backdrops are styled instantly
const glassmorphismObserver = new MutationObserver(() => {
    applyGlassmorphismBackdrop();
});

document.addEventListener('DOMContentLoaded', () => {
    applyGlassmorphismBackdrop();
    glassmorphismObserver.observe(document.body, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class', 'style', 'data-hs-overlay-backdrop-template']
    });
});

// Also trigger on click of any modal trigger button
document.addEventListener('click', (e) => {
    if (e.target.closest('[data-hs-overlay]')) {
        setTimeout(applyGlassmorphismBackdrop, 10);
        setTimeout(applyGlassmorphismBackdrop, 100);
        setTimeout(applyGlassmorphismBackdrop, 300);
    }
});
