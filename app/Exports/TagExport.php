<?php

namespace App\Exports;

use App\Models\Tag;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TagExport implements FromQuery, WithTitle, WithMapping, WithHeadings
{
    public function title(): string
    {
      return 'List Master Tag';
    }

    public function query()
    {
      return Tag::query()
                ->active()
                ->orderby('id', 'asc')
                ->select('id', 'tag_name');
    }

    public function map($tag): array
    {
      return [
        $tag->id,
        $tag->tag_name,
      ];
    }

    public function headings(): array
    {
      return [
        'ID',
        'Tag Name'
      ];
    }
}
