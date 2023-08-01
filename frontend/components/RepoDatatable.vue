<template>
    <div>
        <datatable v-bind="tableProps" no-auto-focus class="q-mb-md" />

    </div>
</template>

<script setup>
import { column } from './datatableColumnBuilder.js'
import Datatable from './Datatable.vue'

const tableProps = {
    url: 'repos',

    paginationDefaults: {
        sort_by: 'name',
        descending: false,
    },

    columns: [
        column('id', 'ID#').setUnsortable(),
        column('name', 'Repo Name'),
        column('stargazers_count', 'Popularity'),
        column('updated_at', 'Activity'),
    ],
}


</script>
