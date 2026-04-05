@php
$testimonials = [
    [
        'text' => "Shelf-E has an amazing collection! I found rare Humayun Ahmed books that I couldn't find anywhere else. The delivery was super fast.",
        'name' => 'Nafisa Tabassum',
        'role' => 'Avid Reader',
    ],
    [
        'text' => "The website is incredibly clean and easy to use. Ordering my university textbooks took exactly two minutes. Highly recommended!",
        'name' => 'Tanvir Rahman',
        'role' => 'Student',
    ],
    [
        'text' => "Packaging was top-notch! My books arrived in pristine condition without a single dent on the corners. Very happy with the service.",
        'name' => 'Sadia Islam',
        'role' => 'Book Enthusiast',
    ],
    [
        'text' => "I love the diverse categories on Shelf-E. It's my go-to online store for ordering both academic books and weekend fiction.",
        'name' => 'Rafiqul Islam',
        'role' => 'Teacher',
    ],
    [
        'text' => "Customer support is very responsive. I requested a specific title, and they managed to stock it for me within just a few days!",
        'name' => 'Nusrat Jahan',
        'role' => 'Literature Student',
    ],
    [
        'text' => "The checkout process is seamless and secure. I also appreciate the 7-day return policy, though the books are always perfect.",
        'name' => 'Ayman Chowdhury',
        'role' => 'Freelancer',
    ],
    [
        'text' => "Shelf-E is exactly what we needed. A modern, fast, and reliable online bookstore that guarantees authentic copies.",
        'name' => 'Farhan Siddiqui',
        'role' => 'Bibliophile',
    ],
    [
        'text' => "The physical book quality is fantastic. Knowing I am getting guaranteed authentic copies gives me great peace of mind.",
        'name' => 'Sumaiya Akter',
        'role' => 'Regular Customer',
    ],
    [
        'text' => "Ordered some children's books for my daughter and they arrived right on time. Fantastic service and great prices.",
        'name' => 'Hassan Ali',
        'role' => 'Parent',
    ],
];

$col1 = array_slice($testimonials, 0, 3);
$col2 = array_slice($testimonials, 3, 3);
$col3 = array_slice($testimonials, 6, 3);
@endphp

<section
    class="my-20 relative bg-background overflow-hidden"
    x-data="{
        visible: false,
        init() {
            const observer = new IntersectionObserver(([entry]) => {
                if (entry.isIntersecting) this.visible = true;
            }, { threshold: 0.1 });
            observer.observe(this.$el);
        }
    }"
>
    <div class="container z-10 mx-auto px-4 max-w-7xl">

        {{-- Section header --}}
        <div
            :class="visible ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-5'"
            class="transition-all duration-700 ease-out flex flex-col items-center justify-center max-w-[540px] mx-auto"
        >
            <div class="flex justify-center">
                <div class="border border-border py-1 px-4 rounded-lg text-sm text-muted-foreground tracking-wide">
                    Testimonials
                </div>
            </div>
            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-bold tracking-tighter mt-5 text-foreground text-center">
                What our readers say
            </h2>
            <p class="text-center mt-5 text-muted-foreground">
                Real reviews from book lovers who shop at Shelf-E.
            </p>
        </div>

        {{-- Scrolling columns --}}
        <div
            class="flex justify-center gap-6 mt-10 max-h-[740px] overflow-hidden"
            style="-webkit-mask-image: linear-gradient(to bottom, transparent, black 25%, black 75%, transparent); mask-image: linear-gradient(to bottom, transparent, black 25%, black 75%, transparent);"
        >

            {{-- Column 1 — always visible, 15 s --}}
            <div class="overflow-hidden shrink-0">
                <div style="animation: testimonial-scroll 15s linear infinite;" class="flex flex-col gap-6 pb-6">
                    @foreach(array_merge($col1, $col1) as $t)
                        <div class="p-6 rounded-2xl border border-border bg-card shadow-sm max-w-xs w-full">
                            <p class="text-sm text-foreground leading-relaxed">{{ $t['text'] }}</p>
                            <div class="mt-5 pt-4 border-t border-border">
                                <span class="block text-sm font-semibold text-foreground tracking-tight leading-5">{{ $t['name'] }}</span>
                                <span class="block text-xs text-muted-foreground leading-5 tracking-tight">{{ $t['role'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Column 2 — md+, 19 s --}}
            <div class="hidden md:block overflow-hidden shrink-0">
                <div style="animation: testimonial-scroll 19s linear infinite;" class="flex flex-col gap-6 pb-6">
                    @foreach(array_merge($col2, $col2) as $t)
                        <div class="p-6 rounded-2xl border border-border bg-card shadow-sm max-w-xs w-full">
                            <p class="text-sm text-foreground leading-relaxed">{{ $t['text'] }}</p>
                            <div class="mt-5 pt-4 border-t border-border">
                                <span class="block text-sm font-semibold text-foreground tracking-tight leading-5">{{ $t['name'] }}</span>
                                <span class="block text-xs text-muted-foreground leading-5 tracking-tight">{{ $t['role'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Column 3 — lg+, 17 s --}}
            <div class="hidden lg:block overflow-hidden shrink-0">
                <div style="animation: testimonial-scroll 17s linear infinite;" class="flex flex-col gap-6 pb-6">
                    @foreach(array_merge($col3, $col3) as $t)
                        <div class="p-6 rounded-2xl border border-border bg-card shadow-sm max-w-xs w-full">
                            <p class="text-sm text-foreground leading-relaxed">{{ $t['text'] }}</p>
                            <div class="mt-5 pt-4 border-t border-border">
                                <span class="block text-sm font-semibold text-foreground tracking-tight leading-5">{{ $t['name'] }}</span>
                                <span class="block text-xs text-muted-foreground leading-5 tracking-tight">{{ $t['role'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</section>
