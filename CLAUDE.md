# Shelf-e Project Guidelines

## Core Tech Stack
* **Backend:** Laravel 12 (PHP 8.2+)
* **Frontend:** Tailwind CSS 4, Alpine.js 3
* **Build Tool:** Vite 7
* **Database:** SQLite
* **Auth:** Laravel Breeze
* **Testing:** PHPUnit, Mockery

## AI Development Rules (Strict)
1. **Atomic Execution:** Only implement the specific task requested. Do not attempt to build out the entire feature if the prompt only asks for a single controller or view.
2. **Alpine.js over jQuery/Vanilla:** All frontend reactivity must be handled natively with Alpine.js (`x-data`, `x-on`, etc.). 
3. **Tailwind 4 Utility Classes:** Utilize Tailwind for all styling. Do not write custom CSS unless absolutely necessary for animations or complex grid layouts not supported by utilities.
4. **Fat Models, Skinny Controllers:** Keep business logic inside Eloquent models or dedicated Service classes. Controllers should primarily handle request validation and response formatting.
5. **Testing First:** When modifying business logic or checkout flows, provide the accompanying PHPUnit tests covering edge cases.