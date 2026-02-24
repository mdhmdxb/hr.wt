<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentTemplate extends Model
{
    protected $fillable = ['name', 'slug', 'content', 'variables_help'];

    public static function render(string $content, array $variables): string
    {
        $out = $content;
        foreach ($variables as $key => $value) {
            $out = str_replace(['{{' . $key . '}}', '{{ ' . $key . ' }}'], (string) $value, $out);
        }
        return $out;
    }

    public function renderWith(array $variables): string
    {
        return self::render($this->content ?? '', $variables);
    }
}
