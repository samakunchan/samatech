function reInitialiationNavBar() {
    document.querySelector('.header_area').classList.remove('navbar_fixed');
    document.querySelector('.main_menu').removeAttribute('style');
}
window.addEventListener('scroll', () => {
    document.querySelector('.header_area').classList.add('navbar_fixed');
    if (window.pageYOffset === 0){
        reInitialiationNavBar();
        return window.addEventListener('wheel', event => {
            reInitialiationNavBar();
            if (event.deltaY === -100) {
                return false;
            }
        });
    } else if (window.pageYOffset >= 50) {
        window.addEventListener('wheel', event => {
            if (event.deltaY < 0) {
                document.querySelector('.header_area').classList.add('navbar_fixed');
                document.querySelector('.main_menu').style.transform = 'translateY(80px)';
            }
            if (event.deltaY > 0) {
                document.querySelector('.main_menu').style.transform = 'translateY(-20px)';
            }
        });
    }
});

// window.addEventListener('scroll', (event) => {
//     document.querySelector('.header_area').classList.add('navbar_fixed');
//     if (window.pageYOffset >= '50') {
//         window.addEventListener('wheel', (event) => {
//             if (event.deltaY < 0) {
//                 document.querySelector('.header_area').classList.add('navbar_fixed');
//                 document.querySelector('.main_menu').style.transform = 'translateY(70px)';
//             }
//             if (event.deltaY > 0) {
//                 document.querySelector('.main_menu').style.transform = 'translateY(0)';
//             }
//         })
//     }
// });
