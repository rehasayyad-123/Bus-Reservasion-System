document.addEventListener('DOMContentLoaded', () => {
    const mobileMenu = document.getElementById('mobile-menu');
    const navLinks = document.querySelector('.nav-links');

    // Toggle mobile menu
    mobileMenu.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        mobileMenu.classList.toggle('nav-active');
    });

    // Close mobile menu when a link is clicked
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            if (navLinks.classList.contains('active')) {
                navLinks.classList.remove('active');
                mobileMenu.classList.remove('nav-active');
            }
        });
    });

    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Basic search functionality (for demonstration - will be extended in a real project)
    const searchBusesHero = document.getElementById('search-buses-hero');
    searchBusesHero.addEventListener('click', () => {
        const leavingFrom = document.getElementById('leaving-from-hero').value;
        const goingTo = document.getElementById('going-to-hero').value;
        const journeyDate = document.getElementById('journey-date-hero').value;

        if (leavingFrom && goingTo && journeyDate) {
            // In a real application, you would send this data to a backend
            // or navigate to a search results page.
            alert(`Searching for buses from ${leavingFrom} to ${goingTo} on ${journeyDate}.`);
            // Example: window.location.href = `search-results.html?from=${leavingFrom}&to=${goingTo}&date=${journeyDate}`;
        } else {
            alert('Please fill in all search fields.');
        }
    });

    // Implement a simple testimonial slider (optional, can be enhanced with a library)
    const testimonialSlider = document.querySelector('.testimonial-slider');
    if (testimonialSlider) {
        let scrollAmount = 0;
        const scrollStep = 370; // Width of testimonial-item + gap (350 + 20)

        // You could add navigation arrows here and control scrollAmount
        // For now, it's just an overflow-x: auto with scroll-snap-type for basic sliding.
    }
});