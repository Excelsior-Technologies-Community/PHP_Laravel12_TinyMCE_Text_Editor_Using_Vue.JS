<template>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Preview</h4>
            <a href="/" class="btn btn-secondary btn-sm">Back</a>
        </div>
        <div class="card-body">
            <h2>{{ item.title }}</h2>
            <span
                class="badge mb-3"
                :class="{
                    'bg-success': item.status === 'published',
                    'bg-warning': item.status === 'draft',
                    'bg-secondary': item.status === 'archived'
                }"
            >
                {{ item.status }}
            </span>
            <div v-html="item.description" class="border rounded p-3 bg-white"></div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        const el = document.getElementById('app')
        return {
            item: JSON.parse(el.dataset.item)
        }
    }
}
</script>
