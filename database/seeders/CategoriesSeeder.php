<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Development' => ['Web Development', 'Mobile Development', 'Game Development', 'DevOps'],
            'Design' => ['UI/UX Design', 'Graphic Design', '3D Modeling'],
            'Business' => ['Entrepreneurship', 'Management', 'Finance'],
            'Marketing' => ['Digital Marketing', 'SEO', 'Content Marketing'],
            'IT & Software' => ['Networking', 'Cybersecurity', 'Cloud Computing'],
            'Personal Development' => ['Productivity', 'Leadership', 'Communication'],
        ];

        $order = 0;
        foreach ($categories as $parent => $children) {
            $parentCat = Category::firstOrCreate(
                ['slug' => Str::slug($parent)],
                ['name' => $parent, 'sort_order' => $order++]
            );

            foreach ($children as $childOrder => $child) {
                Category::firstOrCreate(
                    ['slug' => Str::slug($child)],
                    ['name' => $child, 'parent_id' => $parentCat->id, 'sort_order' => $childOrder]
                );
            }
        }
    }
}
