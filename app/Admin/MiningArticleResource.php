<?php

namespace App\Admin;

use JonesRussell\NorthCloud\Admin\ArticleResource;

class MiningArticleResource extends ArticleResource
{
    public function fields(): array
    {
        return array_merge(parent::fields(), [
            ['name' => 'commodities', 'type' => 'belongs-to-many', 'label' => 'Commodities',
                'rules' => ['array'], 'item_rules' => ['exists:commodities,id'],
                'relationship' => 'commodities', 'display_field' => 'name'],
            ['name' => 'companies', 'type' => 'belongs-to-many', 'label' => 'Companies',
                'rules' => ['array'], 'item_rules' => ['exists:companies,id'],
                'relationship' => 'companies', 'display_field' => 'name'],
            ['name' => 'mining_jurisdiction_id', 'type' => 'belongs-to', 'label' => 'Jurisdiction',
                'rules' => ['nullable', 'exists:mining_jurisdictions,id'],
                'relationship' => 'miningJurisdiction', 'display_field' => 'name'],
            ['name' => 'mining_categories', 'type' => 'belongs-to-many', 'label' => 'Categories',
                'rules' => ['array'], 'item_rules' => ['exists:mining_categories,id'],
                'relationship' => 'miningCategories', 'display_field' => 'name'],
        ]);
    }

    public function filters(): array
    {
        return array_merge(parent::filters(), [
            ['name' => 'commodity', 'type' => 'belongs-to', 'label' => 'Commodity',
                'relationship' => 'commodities', 'display_field' => 'name'],
            ['name' => 'jurisdiction', 'type' => 'belongs-to', 'label' => 'Jurisdiction',
                'relationship' => 'miningJurisdiction', 'display_field' => 'name'],
        ]);
    }

    public function tableColumns(): array
    {
        $columns = parent::tableColumns();

        // Insert jurisdiction column after source
        $index = collect($columns)->search(fn ($col) => $col['name'] === 'news_source');
        if ($index !== false) {
            array_splice($columns, $index + 1, 0, [
                ['name' => 'jurisdiction', 'label' => 'Jurisdiction'],
            ]);
        }

        return $columns;
    }

    public function resolveRelationOptions(): array
    {
        $options = parent::resolveRelationOptions();

        $options['commodities'] = \App\Models\Commodity::orderBy('name')->get(['id', 'name']);
        $options['companies'] = \App\Models\Company::orderBy('name')->get(['id', 'name']);
        $options['mining_jurisdiction_id'] = \App\Models\MiningJurisdiction::orderBy('name')->get(['id', 'name']);
        $options['mining_categories'] = \App\Models\MiningCategory::orderBy('name')->get(['id', 'name']);

        return $options;
    }
}
