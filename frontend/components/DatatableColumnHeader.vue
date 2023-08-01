<template>
    <th :align="col.align" :class="{sortable: col.sortable}" class="text-no-wrap" @click="applySort">
        {{ col.label }}
        <q-icon
            v-if="col.sortable && columnIsSorted"
            :color="columnIsSorted ? 'red' : undefined"
            :name="pagination.descending ? 'expand_more' : 'expand_less'"
        />
    </th>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    col: Object,
    pagination: Object,
})

const emit = defineEmits(['reload']) // Tell datatable to reload its data
const columnIsSorted = computed(() => props.pagination.sort_by == props.col.field)

function applySort() {
    if (!props.col.sortable) return

    // Column already sorted? Flip descending prop
    if (columnIsSorted.value) {
        props.pagination.descending = !props.pagination.descending
        triggerReload(false)
    } else {
        props.pagination.sort_by = props.col.field
        props.pagination.descending = false
        triggerReload(true)
    }
}

function triggerReload(resetPaging) {
    if (resetPaging) props.pagination.page = 1
    emit('reload')
}
</script>
