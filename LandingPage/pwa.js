// Service Worker Register 
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('serviceWorker.js')
            .then(registration => {
            })
            .catch(err => {
            });
    });
}