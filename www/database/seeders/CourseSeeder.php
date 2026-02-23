<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            // CBA
            ['course_code' => 'BSBA-FM', 'name' => 'Bachelor of Science in Business Administration Major in Financial Management', 'department_id' => 'CBA'],
            ['course_code' => 'BSBA-HRDM', 'name' => 'Bachelor of Science in Business Administration Major in Human Resource Development Management', 'department_id' => 'CBA'],
            ['course_code' => 'BSCS', 'name' => 'Bachelor of Science in Computer Science', 'department_id' => 'CBA'],
            ['course_code' => 'BSOA', 'name' => 'Bachelor of Science in Office Administration', 'department_id' => 'CBA'],
            ['course_code' => 'BSPA', 'name' => 'Bachelor of Science in Public Administration', 'department_id' => 'CBA'],
            ['course_code' => 'GNRL-CBA', 'name' => 'General', 'department_id' => 'CBA'],
            // CTELA
            ['course_code' => 'BEED', 'name' => 'Bachelor of Elementary Education', 'department_id' => 'CTELA'],
            ['course_code' => 'BSED-ENG', 'name' => 'Bachelor of Secondary Education Major in English', 'department_id' => 'CTELA'],
            ['course_code' => 'BSED-FIL', 'name' => 'Bachelor of Secondary Education Major in Filipino', 'department_id' => 'CTELA'],
            ['course_code' => 'BSED-MATH', 'name' => 'Bachelor of Secondary Education Major in Mathematics', 'department_id' => 'CTELA'],
            ['course_code' => 'BSED-GS', 'name' => 'Bachelor of Secondary Education Major in General Science', 'department_id' => 'CTELA'],
            ['course_code' => 'BSED-VE', 'name' => 'Bachelor of Secondary Education Major in Values Education', 'department_id' => 'CTELA'],
            ['course_code' => 'BCAED', 'name' => 'Bachelor of Culture & Arts Education', 'department_id' => 'CTELA'],
            ['course_code' => 'BPED', 'name' => 'Bachelor of Physical Education', 'department_id' => 'CTELA'],
            ['course_code' => 'BECE', 'name' => 'Bachelor of Early Childhood Education', 'department_id' => 'CTELA'],
            ['course_code' => 'ABE', 'name' => 'Bachelor of Arts in English', 'department_id' => 'CTELA'],
            ['course_code' => 'ABPS', 'name' => 'Bachelor of Arts in Political Science', 'department_id' => 'CTELA'],
            ['course_code' => 'GNRL-CTELA', 'name' => 'General', 'department_id' => 'CTELA'],
            // CHTM
            ['course_code' => 'AHM', 'name' => 'Associate in Hospitality Management', 'department_id' => 'CHTM'],
            ['course_code' => 'BSHM', 'name' => 'Bachelor of Science in Hospitality Management', 'department_id' => 'CHTM'],
            ['course_code' => 'BSTM', 'name' => 'Bachelor of Science in Tourism Management', 'department_id' => 'CHTM'],
            ['course_code' => 'GNRL-CHTM', 'name' => 'General', 'department_id' => 'CHTM'],
            // CoE
            ['course_code' => 'BSCE', 'name' => 'Bachelor of Science in Civil Engineering', 'department_id' => 'CoE'],
            ['course_code' => 'BSGE', 'name' => 'Bachelor of Science in Geodetic Engineering', 'department_id' => 'CoE'],
            ['course_code' => 'GNRL-CoE', 'name' => 'General', 'department_id' => 'CoE'],
            // CCJE
            ['course_code' => 'BSCRIM', 'name' => 'Bachelor of Science in Criminology', 'department_id' => 'CCJE'],
            ['course_code' => 'GNRL-CCJE', 'name' => 'General', 'department_id' => 'CCJE'],
            // CNSM
            ['course_code' => 'BSN', 'name' => 'Bachelor of Science in Nursing', 'department_id' => 'CNSM'],
            ['course_code' => 'DIPMID', 'name' => 'Diploma in Midwifery', 'department_id' => 'CNSM'],
            ['course_code' => 'GNRL-CNSM', 'name' => 'General', 'department_id' => 'CNSM'],
            // GS
            ['course_code' => 'PhDAS', 'name' => 'Doctor of Philosophy in Administration and Supervision', 'department_id' => 'GS'],
            ['course_code' => 'Ed.D Educ Mgt', 'name' => 'Doctor of Education in Educational Management', 'department_id' => 'GS'],
            ['course_code' => 'MAAS', 'name' => 'Master of Arts in Administration and Supervision', 'department_id' => 'GS'],
            ['course_code' => 'MAEE', 'name' => 'Master of Arts in Elementary Education', 'department_id' => 'GS'],
            ['course_code' => 'MAEng', 'name' => 'Master of Arts in English', 'department_id' => 'GS'],
            ['course_code' => 'MAFil', 'name' => 'Master of Arts in Filipino', 'department_id' => 'GS'],
            ['course_code' => 'MAGC', 'name' => 'Master of Arts in Guidance Counseling', 'department_id' => 'GS'],
            ['course_code' => 'MAMath', 'name' => 'Master of Arts in Mathematics', 'department_id' => 'GS'],
            ['course_code' => 'MAEd-Pre-Elem', 'name' => 'Master of Arts in Pre-Elementary Education', 'department_id' => 'GS'],
            ['course_code' => 'MAHE', 'name' => 'Master of Arts in Home Economics', 'department_id' => 'GS'],
            ['course_code' => 'MBA', 'name' => 'Master in Business Administration', 'department_id' => 'GS'],
            ['course_code' => 'MPA', 'name' => 'Master in Public Administration', 'department_id' => 'GS'],
            ['course_code' => 'GNRL-GS', 'name' => 'General', 'department_id' => 'GS'],
            // Levels
            ['course_code' => 'ELEMENTARY', 'name' => 'Elementary Level', 'department_id' => 'ES'],
            ['course_code' => 'JUNIOR HIGH', 'name' => 'Junior High School Level', 'department_id' => 'JHS'],
            ['course_code' => 'SENIOR HIGH', 'name' => 'Senior High School Level', 'department_id' => 'SHS'],
        ];

        foreach ($courses as $course) {
            DB::table('courses')->updateOrInsert(
                ['course_code' => $course['course_code']], // ✅ match table PK
                ['name' => $course['name'], 'department_id' => $course['department_id']]
            );
        }
    }
}
