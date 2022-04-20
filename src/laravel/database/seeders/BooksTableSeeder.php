<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BooksTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // テーブルのクリア
    DB::table('books')->truncate();

    // 初期データ用意（列名をキーとする連想配列）
    $books = [
      [
        'name' => 'PHP Book',
        'price' => 2000,
        'author' => 'PHPER'
      ],
      [
        'name' => 'Laravel Book',
        'price' => 3000,
        'author' => null
      ],
      [
        'name' => 'Ruby Book',
        'price' => 2500,
        'author' => 'Rubylist'
      ]
    ];

    // 登録
    foreach ($books as $book) {
      \App\Models\Book::create($book);
    }
  }
}
