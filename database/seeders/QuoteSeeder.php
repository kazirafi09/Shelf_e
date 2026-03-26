<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Quote;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quotes = [
            ['quote' => 'The only way to do great work is to love what you do.', 'author' => 'Steve Jobs'],
            ['quote' => 'In the middle of every difficulty lies opportunity.', 'author' => 'Albert Einstein'],
            ['quote' => 'It does not matter how slowly you go as long as you do not stop.', 'author' => 'Confucius'],
            ['quote' => 'Everything you’ve ever wanted is on the other side of fear.', 'author' => 'George Addair'],
            ['quote' => 'Success is not final, failure is not fatal: it is the courage to continue that counts.', 'author' => 'Winston Churchill'],
            ['quote' => 'Hardships often prepare ordinary people for an extraordinary destiny.', 'author' => 'C.S. Lewis'],
            ['quote' => 'Believe you can and you\'re halfway there.', 'author' => 'Theodore Roosevelt'],
            ['quote' => 'Happiness is not something ready made. It comes from your own actions.', 'author' => 'Dalai Lama'],
            ['quote' => 'The journey of a thousand miles begins with one step.', 'author' => 'Lao Tzu'],
            ['quote' => 'We are what we repeatedly do. Excellence, then, is not an act, but a habit.', 'author' => 'Aristotle'],
            ['quote' => 'Life is what happens when you\'re busy making other plans.', 'author' => 'John Lennon'],
            ['quote' => 'Do what you can, with what you have, where you are.', 'author' => 'Theodore Roosevelt'],
            ['quote' => 'To be yourself in a world that is constantly trying to make you something else is the greatest accomplishment.', 'author' => 'Ralph Waldo Emerson'],
            ['quote' => 'The mind is everything. What you think you become.', 'author' => 'Buddha'],
            ['quote' => 'Your time is limited, so don\'t waste it living someone else\'s life.', 'author' => 'Steve Jobs'],
            ['quote' => 'An unexamined life is not worth living.', 'author' => 'Socrates'],
            ['quote' => 'The only true wisdom is in knowing you know nothing.', 'author' => 'Socrates'],
            ['quote' => 'If you want to lift yourself up, lift up someone else.', 'author' => 'Booker T. Washington'],
            ['quote' => 'Not all those who wander are lost.', 'author' => 'J.R.R. Tolkien'],
            ['quote' => 'The way to get started is to quit talking and begin doing.', 'author' => 'Walt Disney'],
            ['quote' => 'Don\'t count the days, make the days count.', 'author' => 'Muhammad Ali'],
            ['quote' => 'You miss 100% of the shots you don’t take.', 'author' => 'Wayne Gretzky'],
            ['quote' => 'Whatever you are, be a good one.', 'author' => 'Abraham Lincoln'],
            ['quote' => 'The best way to predict your future is to create it.', 'author' => 'Abraham Lincoln'],
            ['quote' => 'It is never too late to be what you might have been.', 'author' => 'George Eliot'],
            ['quote' => 'Change your thoughts and you change your world.', 'author' => 'Norman Vincent Peale'],
            ['quote' => 'Either you run the day, or the day runs you.', 'author' => 'Jim Rohn'],
            ['quote' => 'I have not failed. I\'ve just found 10,000 ways that won\'t work.', 'author' => 'Thomas Edison'],
            ['quote' => 'Small deeds done are better than great deeds planned.', 'author' => 'Peter Marshall'],
            ['quote' => 'You must be the change you wish to see in the world.', 'author' => 'Mahatma Gandhi'],
            ['quote' => 'What lies behind us and what lies before us are tiny matters compared to what lies within us.', 'author' => 'Ralph Waldo Emerson'],
            ['quote' => 'He who has a why to live can bear almost any how.', 'author' => 'Friedrich Nietzsche'],
            ['quote' => 'That which does not kill us makes us stronger.', 'author' => 'Friedrich Nietzsche'],
            ['quote' => 'Integrity is doing the right thing, even when no one is watching.', 'author' => 'C.S. Lewis'],
            ['quote' => 'The purpose of our lives is to be happy.', 'author' => 'Dalai Lama'],
            ['quote' => 'Action is the foundational key to all success.', 'author' => 'Pablo Picasso'],
            ['quote' => 'The only impossible journey is the one you never begin.', 'author' => 'Tony Robbins'],
            ['quote' => 'I would rather die on my feet than live on my knees.', 'author' => 'Emiliano Zapata'],
            ['quote' => 'Knowing yourself is the beginning of all wisdom.', 'author' => 'Aristotle'],
            ['quote' => 'Limit your "always" and your "nevers".', 'author' => 'Amy Poehler'],
            ['quote' => 'Turn your wounds into wisdom.', 'author' => 'Oprah Winfrey'],
            ['quote' => 'Simplicity is the ultimate sophistication.', 'author' => 'Leonardo da Vinci'],
            ['quote' => 'Well done is better than well said.', 'author' => 'Benjamin Franklin'],
            ['quote' => 'The only thing we have to fear is fear itself.', 'author' => 'Franklin D. Roosevelt'],
            ['quote' => 'Creativity is intelligence having fun.', 'author' => 'Albert Einstein'],
            ['quote' => 'Keep your face always toward the sunshine—and shadows will fall behind you.', 'author' => 'Walt Whitman'],
            ['quote' => 'Life is 10% what happens to us and 90% how we react to it.', 'author' => 'Charles R. Swindoll'],
            ['quote' => 'Dream big and dare to fail.', 'author' => 'Norman Vaughan'],
            ['quote' => 'If you tell the truth, you don\'t have to remember anything.', 'author' => 'Mark Twain'],
            ['quote' => 'Be so good they can\'t ignore you.', 'author' => 'Steve Martin'],
        ];

        foreach ($quotes as $quote) {
            Quote::create($quote);
        }
    }
}