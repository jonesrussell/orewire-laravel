<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMiningArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'intro' => ['nullable', 'string'],
            'raw_text' => ['nullable', 'string'],
            'canonical_url' => ['nullable', 'string', 'url', 'max:2048'],
            'source' => ['nullable', 'string', 'max:2048'],
            'og_url' => ['nullable', 'string', 'url', 'max:2048'],
            'published_date' => ['nullable', 'string', 'date'],
            'publisher' => ['nullable', 'array'],
            'publisher.route_id' => ['nullable', 'string'],
            'publisher.published_at' => ['nullable', 'string', 'date'],
            'publisher.channel' => ['nullable', 'string'],
            'quality_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'topics' => ['nullable', 'array'],
            'topics.*' => ['string', 'max:100'],
            'commodities' => ['nullable', 'array'],
            'commodities.*' => ['string', 'max:100'],
            'companies' => ['nullable', 'array'],
            'companies.*' => ['string', 'max:255'],
            'jurisdictions' => ['nullable', 'array'],
            'jurisdictions.*' => ['string', 'max:100'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['string', 'max:100'],
            'drill_results' => ['nullable', 'array'],
            'drill_results.*' => ['array'],
            'drill_results.*.hole_id' => ['nullable', 'string', 'max:100'],
            'drill_results.*.commodity' => ['nullable', 'string', 'max:100'],
            'drill_results.*.intercept_m' => ['nullable', 'numeric', 'min:0'],
            'drill_results.*.grade' => ['nullable', 'numeric', 'min:0'],
            'drill_results.*.unit' => ['nullable', 'string', 'max:20'],
            'og_image' => ['nullable', 'string', 'url', 'max:2048'],
            'og_title' => ['nullable', 'string', 'max:500'],
            'og_description' => ['nullable', 'string'],
            'author' => ['nullable', 'string', 'max:255'],
        ];
    }
}
