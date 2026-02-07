document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('#carousel .carousel-slide');
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');

    if (!slides.length) {
        return;
    }

    let current = 0;
    let interval = null;

    function updateVisibility() {
        slides.forEach((slide, index) => {
            if (index === current) {
                slide.classList.add('active');
                slide.classList.remove('hidden');
                slide.style.opacity = '1';
            } else {
                slide.classList.remove('active');
                slide.classList.add('hidden');
                slide.style.opacity = '0';
            }
        });
    }

    function showSlide(index) {
        if (!slides.length) return;
        current = (index + slides.length) % slides.length;
        updateVisibility();
    }

    function nextSlide() {
        showSlide(current + 1);
    }

    function prevSlide() {
        showSlide(current - 1);
    }

    function startAuto() {
        stopAuto();
        interval = setInterval(nextSlide, 4000);
    }

    function stopAuto() {
        if (interval) {
            clearInterval(interval);
            interval = null;
        }
    }

    // Init
    updateVisibility();
    startAuto();

    // Boutons
    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            startAuto();
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            startAuto();
        });
    }

    // Pause au survol
    const carousel = document.getElementById('carousel');
    if (carousel) {
        carousel.addEventListener('mouseenter', stopAuto);
        carousel.addEventListener('mouseleave', startAuto);
    }
});
