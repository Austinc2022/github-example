<template>
    <q-card flat>
        <q-card-section>
            <div class="row q-col-gutter-x-lg items-center">

                <div class="col-12 col-xl-auto">
                    <q-input
                        v-model="quicksearch"
                        dense
                        placeholder="Search name"
                        autofocus
                    >
                        <template #append>
                            <q-icon name="search" />
                        </template>
                    </q-input>
                </div>
            </div>
        </q-card-section>

        <div style="height: 2px">
            <q-linear-progress v-if="isLoading" indeterminate size="2px" />
        </div>

        <q-markup-table class="q-mb-none" bordered flat wrap-cells>
            <thead>
                <tr>
                    <datatable-column-header
                        v-for="col in columns"
                        :key="col.name"
                        :col="col"
                        :pagination="pagination"
                        @reload="fetchData"
                    />
                </tr>
            </thead>
            <tbody v-if="rows">
                <tr v-for="row in rows" :key="row[rowKey]">
                    <td
                        v-for="col in columns"
                        :key="col.name"
                        :class="col.classes"
                    >
                        <slot
                            :name="`column-${col.field}`"
                            :value="row[col.field]"
                            :row="row"
                            :column="col"
                        >
                            {{ col.format ? col.format(row[col.field], row) : row[col.field] }}
                        </slot>
                    </td>
                </tr>
            </tbody>
        </q-markup-table>

        <datatable-pagination
            v-if="totalRows"
            :pagination="pagination"
            :total-rows="totalRows"
            @reload="fetchData"
        />
            
        <div v-else-if="!totalRows && !isLoading" class="row justify-center">
            <placeholder>
                No matching records found
            </placeholder>
        </div>
    </q-card>
</template>

<script setup>
import DatatableColumnHeader from './DatatableColumnHeader.vue'
import DatatablePagination from './DatatablePagination.vue'
import { computed, isReactive, onMounted, reactive, ref, watch } from 'vue'
import { Notify } from 'quasar'

const props = defineProps({
    url: String,
    columns: Array,
    paginationDefaults: Object, // Override default paging settings
    rowKey: {
        type: String,
        default: 'id', // Value in each row data to use to uniquely identify each row
    },
})

const isLoading = ref(true)
const quicksearch = ref()
const totalRows = ref(0)
const pagination = reactive({
    sort_by: 'id',
    descending: false,
    page: 1,
    take: 15,
    ...props.paginationDefaults,
})

const rows = ref([])
const config = useRuntimeConfig()

// Fetch data from the server (server must return a DatatableBuilder response)
async function fetchData() {
    isLoading.value = true

    const response = await useFetch(config.public.apiBaseUrl + props.url, {
        query: {
            quicksearch: quicksearch.value,
            ...pagination,
            skip: (pagination.page - 1) * pagination.take,
        }
    })
    // Replace existing table rows
    // console.log(response.data.value.data)
    rows.value = response.data.value.data
    totalRows.value = response.data.value.total
    isLoading.value = false
}

const fetchDataDebounced = useDebounce(fetchData, 500)

// When user starts typing show loading straight away, but let changes settle (debounce) before hitting the server
watch(quicksearch, () => {
    isLoading.value = true
    // pagination.page = 1
    fetchDataDebounced()
})

// Watch for any external param changes
watch(() => props.url, fetchDataDebounced)

onMounted(fetchData)

</script>
