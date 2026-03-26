import React from 'react';
import { createRoot } from 'react-dom/client';
import PortfolioCarousel from './components/PortfolioCarousel';

// Find the target div in your Blade file
const container = document.getElementById('portfolio-carousel-root');

if (container) {
    const root = createRoot(container);
    
    // Safely grab the database slides passed from Laravel
    const slidesData = JSON.parse(container.getAttribute('data-slides') || '[]');
    
    // If the database has slides, pass them in. Otherwise, use the fallback defaults.
    if (slidesData && slidesData.length > 0) {
        root.render(<PortfolioCarousel slides={slidesData} />);
    } else {
        root.render(<PortfolioCarousel />);
    }
}