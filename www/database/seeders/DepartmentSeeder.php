<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Insert or ignore existing rows
        $departments = [
            ['department_code' => 'CBA', 'name' => 'College of Business Administration'],
            ['department_code' => 'CCJE', 'name' => 'College of Criminal Justice Education'],
            ['department_code' => 'CoE', 'name' => 'College of Engineering'],
            ['department_code' => 'CNSM', 'name' => 'College of Nursing and School of Midwifery'],
            ['department_code' => 'ES', 'name' => 'Elementary School'],
            ['department_code' => 'JHS', 'name' => 'Junior High School'],
            ['department_code' => 'SHS', 'name' => 'Senior High School'],
            ['department_code' => 'CTELA', 'name' => 'College of Teacher Education and Liberal Arts'],
            ['department_code' => 'CHTM', 'name' => 'College of Hospitality and Tourism Management'],
            ['department_code' => 'GS', 'name' => 'Graduate School'],
            ['department_code' => 'RD', 'name' => 'Research Department'],
        ];

        foreach ($departments as $dept) {
            DB::table('departments')->updateOrInsert(
                ['department_code' => $dept['department_code']], // key to check
                ['name' => $dept['name']] // values to insert or update
            );
        }
    }
}