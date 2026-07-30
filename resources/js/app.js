import './bootstrap';
import 'preline';

window.addEventListener('load', () => {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
});
