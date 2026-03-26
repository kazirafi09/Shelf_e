import React, { useState, useEffect, useRef } from 'react';

// Fallback dummy data if you aren't passing props yet
const defaultSlides = [
  { image_path: 'https://images.unsplash.com/photo-1558655146-d09347e92766?q=80&w=800', tag: '#01', title: 'Strategy & Planning' },
  { image_path: 'https://images.unsplash.com/photo-1523381210434-271e8be1f52b?q=80&w=800', tag: '#02', title: 'Design & Development' },
  { image_path: 'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?q=80&w=800', tag: '#03', title: 'Launch & Growth' },
  { image_path: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=800', tag: '#04', title: 'Ongoing Support' },
  { image_path: 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?q=80&w=800', tag: '#05', title: 'Brand Identity' }
];

export default function PortfolioCarousel({ slides = defaultSlides }) {
  const [active, setActive] = useState(2);
  const [isHovered, setIsHovered] = useState(false);
  const touchStartX = useRef(0);
  const touchEndX = useRef(0);

  const total = slides.length;

  // Handle AutoPlay
  useEffect(() => {
    if (isHovered) return;
    const interval = setInterval(() => {
      setActive((prev) => (prev + 1) % total);
    }, 3500);
    return () => clearInterval(interval);
  }, [isHovered, total]);

  // Calculate infinite 3D offset
  const getOffset = (index) => {
    let offset = index - active;
    const half = Math.floor(total / 2);
    if (offset > half) offset -= total;
    if (offset < -half) offset += total;
    return offset;
  };

  // Drag & Swipe Handlers
  const handleDragStart = (e) => {
    setIsHovered(true);
    touchStartX.current = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
  };

  const handleDragMove = (e) => {
    touchEndX.current = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
  };

  const handleDragEnd = () => {
    setIsHovered(false);
    const distance = touchStartX.current - touchEndX.current;
    const threshold = 50; // Minimum drag distance to trigger slide

    if (distance > threshold) {
      setActive((prev) => (prev + 1) % total); // Swipe Left -> Next
    } else if (distance < -threshold) {
      setActive((prev) => (prev - 1 + total) % total); // Swipe Right -> Prev
    }
  };

  return (
    <section className="relative flex flex-col items-center justify-center w-full py-20 overflow-hidden bg-white min-h-[700px]">
      
      {/* 1. Top Text & Header Section */}
      <div className="relative z-20 px-4 mb-16 text-center">
        <span className="block mb-3 text-sm font-bold text-orange-500">
          Behind the Designs
        </span>
        <h2 className="mb-4 text-4xl font-extrabold tracking-tight text-gray-900 md:text-5xl lg:text-6xl">
          Curious What Else I’ve Created?
        </h2>
        <p className="max-w-2xl mx-auto mb-8 text-base font-medium text-gray-500 md:text-lg">
          Explore more brand identities, packaging, and digital design work in my extended portfolio.
        </p>
        
        {/* Rounded CTA Button */}
        <a 
          href="#projects" 
          className="inline-flex items-center justify-center py-2 pl-6 pr-2 text-sm font-bold text-gray-900 transition-all bg-white border border-gray-200 rounded-full shadow-sm hover:shadow-md hover:border-gray-300"
        >
          See more Projects 
          <span className="flex items-center justify-center w-8 h-8 ml-4 text-white transition-transform bg-orange-500 rounded-full group-hover:translate-x-1">
            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
          </span>
        </a>
      </div>

      {/* 2. The 3D Curved Track */}
      <div 
        className="relative flex justify-center items-center w-full h-[400px] md:h-[500px] cursor-grab active:cursor-grabbing select-none" 
        style={{ perspective: '1500px' }}
        onMouseEnter={() => setIsHovered(true)}
        onMouseLeave={() => setIsHovered(false)}
        onTouchStart={handleDragStart}
        onTouchMove={handleDragMove}
        onTouchEnd={handleDragEnd}
        onMouseDown={handleDragStart}
        onMouseMove={(e) => { if (e.buttons === 1) handleDragMove(e); }}
        onMouseUp={handleDragEnd}
      >
        {slides.map((slide, index) => {
          const offset = getOffset(index);
          const absOffset = Math.abs(offset);
          const isCenter = offset === 0;
          
          return (
            <div 
              key={index}
              className="absolute flex flex-col items-center transition-all duration-700 ease-out"
              style={{
                // Responsive translation: smaller gap on mobile, wider on desktop
                transform: `translateX(${offset * (window.innerWidth < 768 ? 200 : 320)}px) 
                            scale(${1 - absOffset * 0.15}) 
                            rotateY(${offset * -20}deg)`,
                zIndex: 50 - absOffset,
                opacity: absOffset >= 3 ? 0 : 1,
                pointerEvents: isCenter ? 'auto' : 'none',
              }}
            >
              {/* Image Card */}
              <div 
                className="bg-center bg-cover border border-gray-200 shadow-xl transition-shadow duration-300 hover:shadow-2xl w-[260px] h-[340px] md:w-[320px] md:h-[420px] rounded-[24px]"
                onClick={() => setActive(index)}
                style={{ backgroundImage: `url('${slide.image_path}')` }}
              />
              
              {/* Card Labels (Fades out when not centered) */}
              <div 
                className={`mt-6 text-center transition-opacity duration-500 ${isCenter ? 'opacity-100' : 'opacity-0'}`}
              >
                <span className="text-sm font-black text-orange-500">
                  {slide.tag}
                </span>
                <h3 className="mt-1 text-xl font-bold text-gray-900">
                  {slide.title}
                </h3>
              </div>
            </div>
          );
        })}
      </div>
    </section>
  );
}