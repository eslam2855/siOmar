<?php

namespace App;

trait SeederHelper
{
    /**
     * Create or update a model with the given data
     *
     * @param string $modelClass
     * @param array $searchAttributes
     * @param array $data
     * @return mixed
     */
    protected function createOrUpdate($modelClass, array $searchAttributes, array $data)
    {
        return $modelClass::updateOrCreate($searchAttributes, $data);
    }

    /**
     * Create a model only if it doesn't exist
     *
     * @param string $modelClass
     * @param array $searchAttributes
     * @param array $data
     * @return mixed
     */
    protected function createIfNotExists($modelClass, array $searchAttributes, array $data)
    {
        return $modelClass::firstOrCreate($searchAttributes, $data);
    }

    /**
     * Create multiple records without duplicates
     *
     * @param string $modelClass
     * @param array $records
     * @param string $uniqueField
     * @return void
     */
    protected function createMultipleWithoutDuplicates($modelClass, array $records, string $uniqueField = 'name')
    {
        foreach ($records as $record) {
            $searchAttributes = [$uniqueField => $record[$uniqueField]];
            $this->createOrUpdate($modelClass, $searchAttributes, $record);
        }
    }
}
