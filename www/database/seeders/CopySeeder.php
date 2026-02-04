<?php

namespace Database\Seeders;

use App\Models\Copy;
use App\Models\Book;
use App\Models\Course;
use Illuminate\Database\Seeder;

class CopySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = Book::all();
        $courses = Course::pluck('course_code')->toArray();

        if ($books->isEmpty()) {
            $this->command->warn('No books found. Please run BookSeeder first.');
            return;
        }

        if (empty($courses)) {
            $this->command->warn('No courses found. Please run CourseSeeder first.');
            return;
        }

        $statuses = ['available', 'borrowed', 'reserved', 'maintenance'];

        foreach ($books as $book) {
            // Create copies based on the number specified in the book's copies field
            for ($i = 1; $i <= $book->copies; $i++) {
                // Generate copy_id: BOOK{book_id}-COPY{copy_number}
                $copyId = sprintf('BOOK%d-COPY%d', $book->id, $i);

                // Randomly assign a course from the same department or general courses
                $departmentCourses = Course::where('department_id', $book->department_id)
                    ->pluck('course_code')
                    ->toArray();

                // If no courses for this department, use any course
                $availableCourses = !empty($departmentCourses) ? $departmentCourses : $courses;

                $randomCourse = $availableCourses[array_rand($availableCourses)];

                // Most copies should be available, some borrowed, few in other statuses
                $status = $this->getWeightedStatus($statuses);

                Copy::create([
                    'copy_id' => $copyId,
                    'book_id' => $book->id,
                    'course_id' => $randomCourse,
                    'status' => $status,
                ]);
            }
        }

        $this->command->info('Copies seeded successfully!');
    }

    /**
     * Get a weighted random status (more available, fewer others)
     */
    private function getWeightedStatus(array $statuses): string
    {
        $random = rand(1, 100);

        if ($random <= 70) {
            return 'available'; // 70% chance
        } elseif ($random <= 85) {
            return 'borrowed'; // 15% chance
        } elseif ($random <= 95) {
            return 'reserved'; // 10% chance
        } else {
            return 'maintenance'; // 5% chance
        }
    }
}
