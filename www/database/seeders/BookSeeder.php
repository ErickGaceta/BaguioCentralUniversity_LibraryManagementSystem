<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'Introduction to Algorithms',
                'author' => 'Thomas H. Cormen, Charles E. Leiserson, Ronald L. Rivest, Clifford Stein',
                'publication_date' => '2009-07-31',
                'publisher' => 'MIT Press',
                'isbn' => '978-0262033848',
                'department_id' => 'CoE',
                'category' => 'Computer Science',
                'copies' => 5,
            ],
            [
                'title' => 'Principles of Management',
                'author' => 'Stephen P. Robbins, Mary Coulter',
                'publication_date' => '2017-01-13',
                'publisher' => 'Pearson',
                'isbn' => '978-0134479545',
                'department_id' => 'CBA',
                'category' => 'Business Management',
                'copies' => 8,
            ],
            [
                'title' => 'Criminal Justice: A Brief Introduction',
                'author' => 'Frank Schmalleger',
                'publication_date' => '2019-01-11',
                'publisher' => 'Pearson',
                'isbn' => '978-0135186251',
                'department_id' => 'CCJE',
                'category' => 'Criminal Justice',
                'copies' => 6,
            ],
            [
                'title' => 'Fundamentals of Nursing',
                'author' => 'Patricia A. Potter, Anne Griffin Perry',
                'publication_date' => '2020-02-21',
                'publisher' => 'Elsevier',
                'isbn' => '978-0323677721',
                'department_id' => 'CNSM',
                'category' => 'Nursing',
                'copies' => 10,
            ],
            [
                'title' => 'Educational Psychology',
                'author' => 'Anita Woolfolk',
                'publication_date' => '2018-01-05',
                'publisher' => 'Pearson',
                'isbn' => '978-0134532066',
                'department_id' => 'CTELA',
                'category' => 'Education',
                'copies' => 7,
            ],
            [
                'title' => 'Professional Cooking',
                'author' => 'Wayne Gisslen',
                'publication_date' => '2018-03-06',
                'publisher' => 'Wiley',
                'isbn' => '978-1119399612',
                'department_id' => 'CHTM',
                'category' => 'Culinary Arts',
                'copies' => 5,
            ],
            [
                'title' => 'Tourism Management: An Introduction',
                'author' => 'Clare Inkson, Lynn Minnaert',
                'publication_date' => '2018-02-16',
                'publisher' => 'SAGE Publications',
                'isbn' => '978-1473958975',
                'department_id' => 'CHTM',
                'category' => 'Tourism',
                'copies' => 4,
            ],
            [
                'title' => 'Engineering Mechanics: Statics',
                'author' => 'J.L. Meriam, L.G. Kraige',
                'publication_date' => '2016-06-21',
                'publisher' => 'Wiley',
                'isbn' => '978-1118807330',
                'department_id' => 'CoE',
                'category' => 'Engineering',
                'copies' => 6,
            ],
            [
                'title' => 'Research Methods in Education',
                'author' => 'Louis Cohen, Lawrence Manion, Keith Morrison',
                'publication_date' => '2017-09-19',
                'publisher' => 'Routledge',
                'isbn' => '978-1138209886',
                'department_id' => 'RD',
                'category' => 'Research Methodology',
                'copies' => 5,
            ],
            [
                'title' => 'Financial Accounting',
                'author' => 'Jerry J. Weygandt, Paul D. Kimmel, Donald E. Kieso',
                'publication_date' => '2018-11-19',
                'publisher' => 'Wiley',
                'isbn' => '978-1119491637',
                'department_id' => 'CBA',
                'category' => 'Accounting',
                'copies' => 9,
            ],
            [
                'title' => 'Child Development',
                'author' => 'Laura E. Berk',
                'publication_date' => '2017-01-02',
                'publisher' => 'Pearson',
                'isbn' => '978-0134419657',
                'department_id' => 'ES',
                'category' => 'Child Psychology',
                'copies' => 4,
            ],
            [
                'title' => 'Introduction to Psychology',
                'author' => 'James W. Kalat',
                'publication_date' => '2016-01-01',
                'publisher' => 'Cengage Learning',
                'isbn' => '978-1305271555',
                'department_id' => 'CTELA',
                'category' => 'Psychology',
                'copies' => 8,
            ],
            [
                'title' => 'Advanced Statistics for Graduate Research',
                'author' => 'Bruce Thompson',
                'publication_date' => '2006-05-15',
                'publisher' => 'SAGE Publications',
                'isbn' => '978-1412905084',
                'department_id' => 'GS',
                'category' => 'Statistics',
                'copies' => 3,
            ],
            [
                'title' => 'Adolescent Psychology',
                'author' => 'John W. Santrock',
                'publication_date' => '2018-01-09',
                'publisher' => 'McGraw-Hill Education',
                'isbn' => '978-1259870897',
                'department_id' => 'JHS',
                'category' => 'Psychology',
                'copies' => 5,
            ],
            [
                'title' => 'College and Career Readiness',
                'author' => 'David T. Conley',
                'publication_date' => '2010-03-26',
                'publisher' => 'Jossey-Bass',
                'isbn' => '978-0470551653',
                'department_id' => 'SHS',
                'category' => 'Career Development',
                'copies' => 6,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}