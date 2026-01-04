<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        // Root sections
        $hq = Section::create(['name' => 'Head Office', 'slug' => 'head-office', 'code' => 'HQ', 'sort_order' => 1]);
        $ops = Section::create(['name' => 'Operations', 'slug' => 'operations', 'code' => 'OPS', 'sort_order' => 2]);
        $fin = Section::create(['name' => 'Finance', 'slug' => 'finance', 'code' => 'FIN', 'sort_order' => 3]);
        $hr  = Section::create(['name' => 'Human Resources', 'slug' => 'human-resources', 'code' => 'HR', 'sort_order' => 4]);

        // Children
        Section::create(['name' => 'Payroll', 'slug' => 'payroll', 'code' => 'PAY', 'parent_id' => $fin->id, 'sort_order' => 1]);
        Section::create(['name' => 'Accounts Receivable', 'slug' => 'accounts-receivable', 'code' => 'AR', 'parent_id' => $fin->id, 'sort_order' => 2]);
        Section::create(['name' => 'Recruitment', 'slug' => 'recruitment', 'code' => 'REC', 'parent_id' => $hr->id, 'sort_order' => 1]);

        // Random demo
        Section::factory()->count(5)->create();
    }
}
